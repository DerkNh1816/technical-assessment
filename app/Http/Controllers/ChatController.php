<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use OpenAI;
use OpenAI\Client;

class ChatController extends Controller
{
    // code only intended for demonstration purposes (ugly code)
    public function chat(Request $request)
    {
        $client = $this->getClient();

        $assistantId = 'asst_13T6M4UgwcDPq2ymEfpY4vyR';
        $assistantName = 'Chatbot';
        $vectorStoreId = null;

        $startDate = Carbon::now();

        $firstMessage = null;

        // $assitant = $client->assistants()->retrieve('asst_13T6M4UgwcDPq2ymEfpY4vyR');
        $threadId = $request->threadId;
        if (! $threadId) {
            $messages = [
                [
                    'role' => 'user',
                    'content' => $request->message,
                ],
            ];

            if ($firstMessage) {
                // prepend the first message to messages
                array_unshift($messages, [
                    'role' => 'assistant',
                    'content' => $firstMessage,
                    'metadata' => [
                        'is_first_message' => 'true',
                    ],
                ]);
            }

            $response = $client->threads()->create(
                [
                    'messages' => $messages,
                ]
            );
            $threadId = $response->id;
        } else {
            $response = $client->threads()->messages()->create($request->threadId, [
                'role' => 'user',
                'content' => $request->message,
            ]);
        }

        $parameters = [
            'assistant_id' => $assistantId,
        ];

        if ($vectorStoreId) {
            $parameters['tools'] = [
                ['type' => 'file_search'],
            ];
            $parameters['tool_resources'] = [
                'file_search' => [
                    'vector_store_ids' => [$vectorStoreId],
                ],
            ];
        }

        $stream = $client->threads()->runs()->createStreamed(
            threadId: $threadId,
            parameters: $parameters
        );

        $messages = [];
        $runs = [];

        do {
            foreach ($stream as $response) {

                switch ($response->event) {
                    case 'thread.run.created':
                    case 'thread.run.queued':
                    case 'thread.run.completed':
                    case 'thread.run.cancelling':
                        $run = $response->response;
                        $runs[] = $run;
                        break;
                    case 'thread.run.expired':
                    case 'thread.run.cancelled':
                    case 'thread.run.failed':
                        $run = $response->response;
                        $runs[] = $run;
                        break 3;
                    case 'thread.run.requires_action':
                        $run = $response->response;
                        $runs[] = $run;
                        // Overwrite the stream with the new stream started by submitting the tool outputs
                        $stream = $client->threads()->runs()->submitToolOutputsStreamed(
                            threadId: $run->threadId,
                            runId: $run->id,
                            parameters: [
                                'tool_outputs' => [
                                    [
                                        'tool_call_id' => 'call_KSg14X7kZF2WDzlPhpQ168Mj',
                                        'output' => '12',
                                    ],
                                ],
                            ]
                        );
                        break;
                }
            }
        } while ($run->status != 'completed');

        return response()->json([
            'threadId' => $threadId,
            'assistantId' => $assistantId,
            'assistantName' => $assistantName,
            'messages' => $this->getMessages($client, $threadId, true, $startDate),
        ]);
    }

    public function get(Request $request, $threadId)
    {
        $client = $this->getClient();
        if ($threadId) {
            return response()->json([
                'messages' => $this->getMessages($client, $threadId),
            ]);
        }

    }

    private function getMessages($client, $threadId, $noviOnly = false, $startDate = null)
    {
        $response = $client->threads()->messages()->list($threadId);
        $messages = [];
        $responseMessages = [];
        if (@$response->data && count($response->data) > 0) {
            $responseMessages = array_reverse(array_filter($response->data, function ($message) use ($noviOnly) {
                return $message->object == 'thread.message' && ($noviOnly ? $message->role === 'assistant' : true);
            }));
        }
        foreach ($responseMessages as $index => $message) {

            $text = @$message->content[0]->text->value;

            $date = Carbon::createFromTimestamp($message->createdAt);

            $isNewestMessage = $index == count($response->data) - 1;

            // if we have a start date, only include messages after that date
            if ($startDate && $date->lt($startDate)) {
                if (! $isNewestMessage) {
                    continue;
                }
            }

            if ($message->metadata && @$message->metadata['is_first_message'] == 'true') {
                continue;
            }

            $newMessage = [
                'author' => $message->role === 'assistant' ? 'Novi' : $message->role,
                'created' => $date->format('Y-m-d H:i:s'),
            ];

            $itemsToParse = ['voorwaarden', 'telefoonnummer', 'email'];

            // get all matches for [voorwaarden:"index"]
            $items = [];
            foreach ($itemsToParse as $item) {
                $regex = '/\['.$item.':(.+)\]/';
                if (preg_match_all($regex, $text, $matches)) {
                    // add all found matches to the newMessage
                    foreach ($matches[1] as $match) {
                        $falseMatches = ['n.v.t', 'nvt', 'vul hier telefoonnummer in', 'vul hier email in', 'vul hier telefoonnummer in'];
                        if (! $match || in_array(strtolower($match), $falseMatches)) {
                            continue;
                        }
                        $items[] = [
                            'type' => $item,
                            'value' => $match,
                        ];
                    }
                    $text = preg_replace($regex, '', $text);
                }
            }
            $newMessage['items'] = $items;

            // also remove the [bronnen] from the text
            $text = str_replace('[bronnen]', '', $text);

            $newMessage['message'] = trim($text);

            $messages[] = $newMessage;

        }
        // sort by created
        if (count($messages) > 1) {
            usort($messages, function ($a, $b) {
                return Carbon::parse($a['created'])->gt(Carbon::parse($b['created'])) ? 1 : -1;
            });
        }

        return $messages;

    }

    private function getClient(): Client
    {
        $baseUri = config('env.AZURE_OPENAI_ENDPOINT').'/openai';
        $client = OpenAI::factory()
            ->withBaseUri($baseUri)
            ->withHttpHeader('api-key', config('env.AZURE_OPENAI_API_KEY'))
            ->withQueryParam('api-version', '2024-08-01-preview')
            ->make();

        return $client;
    }
}

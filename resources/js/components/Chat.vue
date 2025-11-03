<template>
    <div>
        <v-fab-transition appear>
            <v-fab
                icon="mdi-chat"
                variant="tonal"
                bottom
                absolute
                class="ms-4 chat-fab"
                size="large"
                right
                @click="toggleChat"
            >
            </v-fab>
        </v-fab-transition>
        <v-expand-transition tag="div">
            <div v-if="showChat" class="chat-wrapper">
                <v-card elevation="2">
                    <v-row class="no-gutters">
                        <v-col cols="auto" class="flex-grow-1 flex-shrink-0">
                            <v-responsive class="fill-height">
                                <v-card
                                    max-width="500"
                                    max-height="80vh"
                                    class="d-flex flex-column fill-height"
                                >
                                    <ChatAppBar
                                        :title="currentAssistantName"
                                        @drawerClick="drawer = !drawer"
                                    />
                                    <v-card-text
                                        id="chat-chat"
                                        class="flex-grow-1 overflow-y-auto chat-scroller"
                                    >
                                        <template v-for="(msg, i) in messages">
                                            <div
                                                v-if="msg.message !== 'init'"
                                                :id="'message-' + i"
                                                :ref="'message-' + i"
                                                :key="i"
                                                :class="{
                                                    'chat-message d-flex flex-row-reverse':
                                                        msg.author !== 'Chat',
                                                }"
                                            >
                                                <v-hover v-slot="{ hover }">
                                                    <v-chip
                                                        :color="
                                                            msg.error &&
                                                            msg.author == 'Chat'
                                                                ? 'error'
                                                                : msg.author !==
                                                                    'Chat'
                                                                  ? 'primary'
                                                                  : ''
                                                        "
                                                        style="
                                                            height: auto;
                                                            white-space: normal;
                                                            border-radius: 24px;
                                                            max-width: 95%;
                                                        "
                                                        class="pa-4 mb-2"
                                                    >
                                                        <div
                                                            style="
                                                                position: relative;
                                                            "
                                                        >
                                                            <div
                                                                v-if="
                                                                    msg.author ===
                                                                        'Chat' &&
                                                                    currentTyping ===
                                                                        i
                                                                "
                                                                id="currentTyping"
                                                                class="typewriter-text"
                                                            ></div>
                                                            <span
                                                                v-if="
                                                                    msg.author ===
                                                                        'Chat' &&
                                                                    currentTyping ===
                                                                        i
                                                                "
                                                                class="typewriter chat-message"
                                                                v-html="
                                                                    msg.message
                                                                "
                                                            ></span>
                                                            <span
                                                                v-else
                                                                class="chat-message"
                                                                v-html="
                                                                    msg.message
                                                                "
                                                            ></span>
                                                        </div>
                                                        <sub
                                                            v-if="msg.created"
                                                            class="ml-2"
                                                            style="
                                                                font-size: 0.5rem;
                                                            "
                                                        >
                                                            {{
                                                                getTime(
                                                                    msg.created
                                                                )
                                                            }}
                                                        </sub>
                                                        <v-menu>
                                                            <template
                                                                #activator="{
                                                                    attrs,
                                                                    on,
                                                                    value,
                                                                }"
                                                            >
                                                                <div
                                                                    v-bind="
                                                                        attrs
                                                                    "
                                                                >
                                                                    <v-icon
                                                                        v-if="
                                                                            hover ||
                                                                            value
                                                                        "
                                                                        style="
                                                                            position: absolute;
                                                                            right: 0;
                                                                        "
                                                                        size="x-small"
                                                                        class="mr-1"
                                                                        v-on="
                                                                            on
                                                                        "
                                                                    >
                                                                        mdi-chevron-down
                                                                    </v-icon>
                                                                </div>
                                                            </template>
                                                            <v-list>
                                                                <v-list-item
                                                                    @click="
                                                                        () =>
                                                                            chatStore.deleteMessage(
                                                                                i
                                                                            )
                                                                    "
                                                                >
                                                                    <v-list-item-title>
                                                                        Delete
                                                                        message
                                                                    </v-list-item-title>
                                                                </v-list-item>
                                                            </v-list>
                                                        </v-menu>
                                                    </v-chip>
                                                </v-hover>
                                            </div>
                                            <ChatMessageInfo
                                                v-if="msg.items"
                                                :key="'info-' + i"
                                                :items="msg.items"
                                                :show="currentTyping !== i"
                                            />
                                        </template>
                                        <div
                                            v-if="sending"
                                            class="dots-wrapper"
                                        >
                                            <span class="dots-cont">
                                                <span class="dot dot-1"></span>
                                                <span class="dot dot-2"></span>
                                                <span class="dot dot-3"></span>
                                            </span>
                                        </div>
                                    </v-card-text>
                                    <v-card-text class="flex-shrink-1 pt-0">
                                        <v-text-field
                                            v-model="currentMessage"
                                            autofocus
                                            :disabled="sending"
                                            :rules="[
                                                (v) =>
                                                    (v || '').length <= 560 ||
                                                    'Message must be 560 characters or less',
                                            ]"
                                            type="text"
                                            no-details
                                            outlined
                                            append-icon="mdi-send"
                                            hide-details
                                            @keyup.enter="
                                                () =>
                                                    sendMessage(currentMessage)
                                            "
                                            @click:append="
                                                () =>
                                                    sendMessage(currentMessage)
                                            "
                                        />
                                    </v-card-text>
                                </v-card>
                            </v-responsive>
                        </v-col>
                    </v-row>
                    <v-navigation-drawer
                        v-model="drawer"
                        absolute
                        right
                        temporary
                    >
                        <ChatAppDrawerItems
                            @onChatSwitch="onChatSwitch"
                            @closeDrawer="drawer = false"
                        />
                    </v-navigation-drawer>
                </v-card>
            </div>
        </v-expand-transition>
    </div>
</template>

<script>
import { mapStores } from 'pinia'
import { useChatStore } from '../store/modules/chat.js'
import ChatAppBar from './ChatAppBar.vue'
import ChatAppDrawerItems from './ChatAppDrawerItems.vue'
import ChatMessageInfo from './ChatMessageInfo.vue'
import axios from 'axios'

export default {
    components: {
        ChatAppBar,
        ChatAppDrawerItems,
        ChatMessageInfo,
    },
    data: () => ({
        sending: false,
        currentMessage: null,
        currentTyping: null,
        initing: false,
        drawer: false,
        items: [],
        resizeObserver: null,
        mutationObserver: null,
        showChat: false,
        interval: null,
        currentAssistantName: null,
    }),
    computed: {
        ...mapStores(useChatStore),
        chats() {
            return this.chatStore?.chats
        },
        currentChat() {
            return this.chatStore?.currentChat
        },
        messages: {
            get() {
                if (
                    !this.chatStore ||
                    this.chats === null ||
                    this.currentChat === null ||
                    !this.chats[this.currentChat]
                ) {
                    return null
                }
                return this.chats[this.currentChat].messages
            },
            deep: true,
        },
    },
    watch: {
        currentChat: {
            handler: function (val) {
                if (this.chats && this.chats[val]) {
                    this.currentAssistantName = this.chats[val].assistantName
                }
            },
            immediate: true,
        },
        messages: {
            handler: function (val) {
                if (
                    !this.initing &&
                    this.chats &&
                    this.chats.length > 0 &&
                    this.messages &&
                    this.messages.length === 0
                ) {
                    // this.initing = true
                    // this.sendMessage('init')
                    if (this.chats[this.currentChat]) {
                        this.currentAssistantName =
                            this.chats[this.currentChat].assistantName
                    }
                }
            },
            deep: true,
            immediate: true,
        },
    },
    mounted() {},
    beforeUnmount() {
        // clearInterval(this.interval)
        this.currentTyping = null
    },
    methods: {
        sendMessage(val) {
            if (val && val.length <= 560 && !this.sending) {
                var today = new Date()
                var message = {
                    message: val,
                    created: today.toJSON(),
                }
                this.chatStore.addMessage(message)

                var sendingMessage = val
                if (val === this.currentMessage) {
                    this.currentMessage = null
                }

                var threadId = null
                var assistantId = null
                if (
                    this.chats &&
                    this.currentChat !== null &&
                    this.chats[this.currentChat]
                ) {
                    threadId = this.chats[this.currentChat].threadId
                    assistantId = this.chats[this.currentChat].assistantId
                }

                this.sending = true
                // dev = 'http://localhost:5000/ai/chat'
                axios
                    .post(
                        '/chat',
                        {
                            threadId,
                            assistantId,
                            message: val,
                        },
                        {
                            hideProgress: true,
                            cancelOnRouteChange: false,
                        }
                    )
                    .then((response) => {
                        if (
                            response &&
                            response.data &&
                            response.data.threadId
                        ) {
                            this.chatStore.setCurrentThreadId(
                                response.data.threadId
                            )
                        }

                        if (
                            response &&
                            response.data &&
                            response.data.assistantId
                        ) {
                            this.chatStore.setCurrentAssistant({
                                id: response.data.assistantId,
                                name: response.data.assistantName,
                            })
                            if (
                                this.currentAssistantName !==
                                response.data.assistantName
                            ) {
                                this.currentAssistantName =
                                    response.data.assistantName
                            }
                        }

                        if (
                            response &&
                            response.data &&
                            response.data.messages
                        ) {
                            response.data.messages.forEach((msg) => {
                                // type animation disabled
                                // this.startTyping(msg.message)
                                this.chatStore.addMessage(msg)
                            })
                        }
                    })
                    .catch((error) => {
                        console.log(error)
                        this.chatStore.errorMessage()
                        if (
                            sendingMessage !== 'init' &&
                            this.currentMessage === null
                        ) {
                            this.currentMessage = sendingMessage
                        }
                        this.chatStore.addMessage({
                            author: 'Chat',
                            error: true,
                            message: 'Sorry, something went wrong.',
                            created: today.toJSON(),
                        })
                    })
                    .finally(() => {
                        this.sending = false
                        this.initing = false
                    })
            }
        },
        getMessages() {
            if (this.currentThreadId) {
                axios
                    .get('/chat/' + this.currentThreadId)
                    .then((response) => {
                        this.chatStore.setChatMessages(response.data)
                    })
                    .catch((error) => {
                        console.log(error)
                    })
            }
        },
        getTime(date) {
            date = new Date(date)
            return date.toLocaleTimeString('nl-NL', {
                hour: 'numeric',
                minute: 'numeric',
            })
        },
        onChatSwitch() {
            if (this.currentTyping !== null) {
                this.currentTyping = null
            }
        },
        scrollBottom() {
            var chat = document.getElementById('chat-chat')
            if (this.messages == null) {
                return
            }
            var latestMessage = document.getElementById(
                'message-' + (this.messages.length - 1)
            )
            if (chat && latestMessage && latestMessage.clientHeight) {
                chat.scrollTop =
                    chat.scrollHeight - (latestMessage.clientHeight + 12)
            } else if (chat) {
                chat.scrollTop = chat.scrollHeight
            }
        },
        toggleChat() {
            this.showChat = !this.showChat
            if (this.showChat) {
                this.addObserver()
            }
        },
        addObserver() {
            this.$nextTick(() => {
                const addResizeObserver = () => {
                    if (this.resizeObserver) {
                        this.resizeObserver.disconnect()
                    }
                    this.resizeObserver = new ResizeObserver(() =>
                        this.scrollBottom()
                    )
                    const chat = document.getElementById('chat-chat')

                    if (chat) {
                        // This is the critical part: We observe the size of all children!
                        for (const child of chat.children) {
                            this.resizeObserver.observe(child)
                        }
                    }
                }

                if (this.mutationObserver) {
                    this.mutationObserver.disconnect()
                }

                this.mutationObserver = new MutationObserver(addResizeObserver)

                const observerOptions = {
                    childList: true,
                }
                const chat = document.getElementById('chat-chat')
                if (chat) {
                    this.mutationObserver.observe(chat, observerOptions)
                }

                this.scrollBottom()
            })

            // this.interval = setInterval(() => {
            //     this.getMessages()
            // }, 10000)
            // this.getMessages()
        },
    },
}
</script>

<style>
.chat-wrapper {
    z-index: 4;
    position: fixed;
    right: 16px;
    bottom: 96px;
}
.typewriter {
    color: transparent;
}
.chat-message {
    white-space: break-spaces;
}
.typewriter-text {
    position: absolute;
    width: inherit;
    color: black;
}

.dots-wrapper {
    height: 24px;
    position: relative;
}

.dots-cont {
    position: absolute;
    left: 0px;
    bottom: 0px;
}
.dot {
    width: 12px;
    height: 12px;
    background: #e4e4e4;
    display: inline-block;
    border-radius: 50%;
    right: 0px;
    bottom: 0px;
    margin: 0px 2.5px;
    position: relative;
    animation: jump 1s infinite;
}
.dots-cont:hover > .dot {
    /* position: relative; */
    /* bottom: 0px; */
    animation: none;
}
.dots-cont .dot-1 {
    -webkit-animation-delay: 100ms;
    animation-delay: 100ms;
}
.dots-cont .dot-2 {
    -webkit-animation-delay: 200ms;
    animation-delay: 200ms;
}
.dots-cont .dot-3 {
    -webkit-animation-delay: 300ms;
    animation-delay: 300ms;
}
@keyframes jump {
    0% {
        bottom: 0px;
    }
    20% {
        bottom: 5px;
    }
    40% {
        bottom: 0px;
    }
}
.chat-scroller {
    max-height: 50vh;
    min-height: 300px;
    min-width: 500px;
}

.chat-fab {
    .v-fab__container {
        bottom: 12px;
        right: 12px;
    }
}
</style>

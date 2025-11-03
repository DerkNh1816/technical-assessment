<template>
    <v-list subheader>
        <template v-for="(item, index) in chats" :key="`parent${index}`">
            <v-hover v-slot="{ hover }">
                <v-list-item
                    :active="index == currentChat"
                    @touchstart="(e) => openDeleteDialogMobile(e, index)"
                    @touchend="() => clearDeleteDialogMobile()"
                    @click="() => switchCurrentChat(index)"
                >
                    <template v-if="item && item.messages.length">
                        <v-list-item-action>
                            <v-icon>mdi-comments</v-icon>
                        </v-list-item-action>
                        <v-list-item-title>
                            {{
                                getDate(
                                    item.messages[item.messages.length - 1]
                                        .created
                                )
                            }}
                            <span class="text-medium-emphasis">
                                {{
                                    getTime(
                                        item.messages[item.messages.length - 1]
                                            .created
                                    )
                                }}
                            </span>
                        </v-list-item-title>
                        <v-list-item-subtitle>
                            <template
                                v-if="
                                    item.messages[item.messages.length - 1]
                                        .author === 'System'
                                "
                            >
                                <span class="text-primary">System</span>
                                -
                            </template>
                            <template
                                v-if="
                                    item.messages[item.messages.length - 1]
                                        .message &&
                                    item.messages[item.messages.length - 1]
                                        .message !== 'init'
                                "
                            >
                                {{
                                    item.messages[item.messages.length - 1]
                                        .message
                                }}
                            </template>
                        </v-list-item-subtitle>
                    </template>
                    <v-slide-x-transition appear>
                        <v-btn
                            v-show="hover"
                            class="mr-1"
                            icon
                            color="primary"
                            small
                            absolute
                            style="
                                right: 0;
                                top: 50%;
                                transform: translateY(-50%);
                            "
                            @click="(e) => openDeleteDialog(e, index)"
                        >
                            <v-icon size="small">mdi-trash</v-icon>
                        </v-btn>
                    </v-slide-x-transition>
                </v-list-item>
            </v-hover>
            <v-divider class="my-0" />
        </template>
        <v-list-item @click="newChat">
            <v-list-item-action>
                <v-icon>mdi-plus</v-icon>
            </v-list-item-action>
            <v-list-item-title>New chat</v-list-item-title>
        </v-list-item>
        <v-dialog v-model="deleteDialog" width="500">
            Are you sure you want to delete this chat conversation?
            <template v-slot:actions>
                <v-btn
                    class="ms-auto"
                    text="No"
                    @click="dialog = false"
                ></v-btn>
                <v-btn class="ms-auto" text="No" @click="removeChat"></v-btn>
            </template>
        </v-dialog>
    </v-list>
</template>

<script>
import { mapStores } from 'pinia'
import { useChatStore } from '../store/modules/chat.js'

export default {
    data: () => ({
        currentDelete: null,
        openDeleteDialogTimeout: null,
        deleteDialog: false,
    }),
    computed: {
        ...mapStores(useChatStore),
        currentChat: {
            get() {
                return this.chatStore.currentChat
            },
            deep: true,
        },
        chats: {
            get() {
                if (!this.chatStore || this.chatStore.chats === null) {
                    return []
                }
                return this.chatStore.chats
            },
            deep: true,
        },
    },
    methods: {
        getDate(date) {
            date = new Date(date).toLocaleDateString()
            return date
        },
        getTime(date) {
            date = new Date(date)
            return date.toLocaleTimeString('nl-NL', {
                hour: 'numeric',
                minute: 'numeric',
            })
        },
        newChat() {
            this.chatStore.addChat()
            this.$emit('closeDrawer')
            this.$emit('onChatSwitch')
        },
        switchCurrentChat(index) {
            this.chatStore.setCurrentChat(index)
            this.$emit('closeDrawer')
            this.$emit('onChatSwitch')
        },
        openDeleteDialog(e, index) {
            e.stopPropagation()
            this.deleteDialog = true
            this.currentDelete = index
        },
        openDeleteDialogMobile(e, index) {
            e.stopPropagation()
            this.openDeleteDialogTimeout = setTimeout(() => {
                this.deleteDialog = true
                this.currentDelete = index
            }, 500)
        },
        clearDeleteDialogMobile() {
            if (this.openDeleteDialogTimeout) {
                clearTimeout(this.openDeleteDialogTimeout)
            }
        },
        removeChat() {
            this.chatStore.deleteChat(this.currentDelete)
            if (
                this.currentDelete - 1 > 0 &&
                this.currentChat === this.currentDelete
            ) {
                this.chatStore.setCurrentChat(this.currentDelete - 1)
                this.$emit('onChatSwitch')
            }
            this.currentDelete = null
        },
    },
}
</script>

import { defineStore } from 'pinia';
var chats = []
var currentChat = 0

if (localStorage) {
    var localLang = null
    try {
        localLang = JSON.parse(localStorage.getItem('lang'))
    } catch {
        localLang = false
    }
    if (localLang) {
        lang = localLang
    }

    var localChats = null
    var localCurrentChat = null
    try {
        localChats = JSON.parse(localStorage.getItem('chats'))
        localCurrentChat = JSON.parse(localStorage.getItem('currentChat'))
    } catch {
        console.log('no localstorage chats found')
    }

    if (localChats !== undefined && localChats !== null) {
        chats = localChats
    }
    if (localCurrentChat !== undefined && localCurrentChat !== null) {
        currentChat = localCurrentChat
    }
}

export const useChatStore = defineStore('chat', {
  actions: {
    addChat() {
      if (this.chats && Array.isArray(this.chats)) {
          this.chats.push({ messages: [] })
          this.setCurrentChat(this.chats.length - 1)
      }
    },
    setCurrentChat(val) {
        this.currentChat = val
        if (localStorage) {
            localStorage.setItem('currentChat', JSON.stringify(val))
        }
    },
    setChatMessages(val) {
        if (
            this.chats.length > 0 &&
            this.currentChat !== null &&
            this.chats[this.currentChat]
        ) {
            var chatIndex = parseInt(this.currentChat)
            this.chats[chatIndex].messages = val
        }
        if (localStorage) {
            localStorage.setItem('chats', JSON.stringify(this.chats))
        }
    },
    addMessage(val) {
        if (!this.chats || this.chats.length === 0) {
            this.chats = [{ messages: [] }]
            this.currentChat = 0
        }
        var chatIndex = parseInt(this.currentChat)
        if (!this.chats[chatIndex]) {
            this.chats[chatIndex] = { messages: [] }
        }
        this.chats[chatIndex].messages.push(val)
        if (localStorage) {
            localStorage.setItem('chats', JSON.stringify(this.chats))
        }
    },
    deleteMessage(index) {
        var chatIndex = parseInt(this.currentChat)
        if (!this.chats[chatIndex]) {
            return
        }
        this.chats[chatIndex].messages.splice(index, 1)
        if (this.chats[chatIndex].messages.length === 0) {
            this.commit('novi/deleteChat', chatIndex)
        }
        if (localStorage) {
            localStorage.setItem('chats', JSON.stringify(this.chats))
        }
    },
    errorMessage() {
        var chatIndex = parseInt(this.currentChat)
        if (!this.chats[chatIndex]) {
            return
        }
        var latestIndex = this.chats[chatIndex].messages.length - 1
        this.chats[chatIndex].messages[latestIndex].error = true
        if (localStorage) {
            localStorage.setItem('chats', JSON.stringify(this.chats))
        }
    },
    deleteChat(index) {
        if (!this.chats[index]) {
            return
        }
        this.chats.splice(index, 1)
        if (localStorage) {
            localStorage.setItem('chats', JSON.stringify(this.chats))
        }
    },
    setCurrentThreadId(val) {
        if (
            this.chats.length > 0 &&
            this.currentChat !== null &&
            this.chats[this.currentChat]
        ) {
            var chatIndex = parseInt(this.currentChat)
            this.chats[chatIndex].threadId = val
        }
        if (localStorage) {
            localStorage.setItem('chats', JSON.stringify(this.chats))
        }
    },
    setCurrentAssistant(val) {
        if (
            this.chats.length > 0 &&
            this.currentChat !== null &&
            this.chats[this.currentChat]
        ) {
            var chatIndex = parseInt(this.currentChat)
            this.chats[chatIndex].assistantId = val?.id
            this.chats[chatIndex].assistantName = val?.name
        }
        if (localStorage) {
            localStorage.setItem('chats', JSON.stringify(this.chats))
        }
    }
  },

  getters: {
  },

  state: () => ({
    chats,
    currentChat
  })
});

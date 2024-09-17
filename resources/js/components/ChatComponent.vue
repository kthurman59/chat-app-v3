<template>
  <div class="chat-component bg-white shadow-xl rounded-lg">
    <div class="messages p-4 h-96 overflow-y-auto" ref="messageContainer">
      <div v-for="message in messages" :key="message.id" class="message mb-4">
        <div class="font-bold">{{ message.user.name }}</div>
        <div class="ml-2">{{ message.content }}</div>
      </div>
    </div>
    <form @submit.prevent="sendMessage" class="p-4 border-t">
      <div class="flex">
        <input v-model="newMessage" type="text" placeholder="Type a message" class="flex-grow mr-2 p-2 border rounded">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Send</button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      messages: [],
      newMessage: '',
    }
  },
  mounted() {
    this.fetchMessages();
    this.listenForMessages();
  },
  methods: {
    async fetchMessages() {
      const response = await fetch('/messages');
      this.messages = await response.json();
      this.scrollToBottom();
    },
    async sendMessage() {
      if (this.newMessage.trim() === '') return;
      
      const response = await fetch('/messages', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ content: this.newMessage })
      });
      
      const message = await response.json();
      this.messages.push(message);
      this.newMessage = '';
      this.scrollToBottom();
    },
    listenForMessages() {
      window.Echo.channel('chat')
        .listen('MessageSent', (event) => {
          this.messages.push(event.message);
          this.scrollToBottom();
        });
    },
    scrollToBottom() {
      this.$nextTick(() => {
        const container = this.$refs.messageContainer;
        container.scrollTop = container.scrollHeight;
      });
    }
  }
}
</script>
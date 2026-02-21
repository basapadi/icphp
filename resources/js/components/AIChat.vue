<template>
  <div class="chat-wrapper">
    <!-- Toggle Button -->
    <button class="chat-toggle" @click="actionOpen">
      💬
    </button>

    <!-- Chat Box -->
    <div v-if="isOpen" class="chat-box bg-white rounded-lg shadow-lg overflow-hidden h-screen flex flex-col">
      <!-- Header -->
      <div class="chat-header flex items-center justify-between bg-blue-500 text-white px-4 py-2 font-bold">
        <div class="flex items-center gap-3">
          <span>Tanya ICAI</span>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" v-model="summary" class="sr-only peer">
            <div class="w-10 h-5 bg-gray-300 rounded-full peer-checked:bg-green-500 transition-all"></div>
            <span
              class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full peer-checked:translate-x-5 transition-transform"></span>
            <span class="ml-2 text-sm">Ringkasan Data</span>
          </label>
        </div>
        <button @click="isOpen = false" class="text-white text-xl font-bold mr-3">×</button>
      </div>

      <!-- Body -->
      <div class="chat-body flex flex-col flex-1 overflow-y-auto p-4 m-4" ref="chatBody">
        <div v-for="(msg, index) in messages" :key="index" class="chat-message break-words p-2 rounded-2xl mb-2 text-xs" :class="[
          msg.from === 'user' ? 'bg-blue-100 shadow-sm self-end max-w-[90%] ring-black/5 ring-1' : 'self-start w-full'
        ]">
          <div v-if="msg.query" class="text-sm text-gray-500">
            <blockquote class="bg-gray-100 border-l-4 border-green-700 px-4 py-1 mb-2 italic text-xs inline-flex text-left text-gray-600 rounded-md w-full overflow-x-auto">
              {{ msg.query }}
            </blockquote>
          </div>
          <div class="markdown-body overflow-x-auto max-w-full pt-2" v-html="renderMarkdown(msg.text)"></div>
        </div>
        <div v-if="botLoading">
          <div class="flex items-left space-x-2">
            <div class="typing"></div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="chat-footer flex items-end gap-2 p-2 border-t border-gray-200">
        <textarea
          ref="inputRef"
          v-model="input"
          rows="1"
          @input="autoResize"
          @keydown.enter.exact.prevent="sendMessage"
          placeholder="Tanya terkait aplikasi ini, ketik /help untuk bantuan"
          class="
            flex-1
            px-4 py-3
            text-sm
            border
            rounded-2xl
            resize-none
            overflow-y-auto
            max-h-40
            shadow-sm
            focus:outline-none
            focus:ring-2 focus:ring-blue-400
            no-scrollbar
          "
        />
        <button v-if="speechSupported" @click="toggleRecording" class="bg-green-500 text-white text-xs px-3 py-2 rounded-lg">
          {{ isRecording ? 'Stop' : 'Mic' }}
        </button>
        <!-- <button @click="sendMessage" class="bg-blue-500 text-white text-xs px-4 py-2 rounded-lg">Kirim</button> -->
      </div>
    </div>

  </div>
</template>

<script>
import { marked } from "marked";
import { ref } from 'vue'
export default {
  watch: {
    messages: {
      deep: true,
      async handler() {
        localStorage.setItem("ai_chat", JSON.stringify(this.messages));
        await this.scrollToBottom()
      }
    }
  },
  data() {
    return {
      isOpen: false,
      messages: localStorage.getItem("ai_chat") ? JSON.parse(localStorage.getItem("ai_chat")).slice(-20) : [],
      input: "",
      summary: false,
      botLoading: false,
      speechSupported: false,
      recognition: null,
      isRecording: false
    };
  },
  methods: {
    toggleRecording() {
      if (!this.isRecording) {
        this.recognition.start();
        this.isRecording = true;
      } else {
        this.recognition.stop();
        this.isRecording = false;
      }
    },
    actionOpen() {
      this.isOpen = !this.isOpen;
      this.scrollToBottom()
    },

    scrollToBottom() {
      this.$nextTick(() => {
        const el = this.$refs.chatBody;
        if (!el) return;
        el.scrollTop = el.scrollHeight;
      });
    },
    sendMessage() {
      if (!this.input.trim()) return;

      const userMsg = this.input.trim();
      if (userMsg == 'clear') {
        localStorage.removeItem('ai_chat');
        this.messages = [];
        this.input = "";
        return;
      }

      if (userMsg == 'rangkum') {
        this.summary = !this.summary;
        this.messages.push({ from: "bot", text: `Mode rangkuman di${this.summary ? 'aktif' : 'non-aktif'}kan` });
        this.input = "";
        return;
      }

      if (userMsg == 'close') {
        this.isOpen = false;
        this.input = "";
        return;
      }

      if(userMsg == '/help'){
        this.messages.push({ from: "bot", text: `
          Beberapa perintah yang bisa Anda gunakan adalah
          - clear   : Untuk menghapus riwayat percakapan.
          - rangkum : Untuk membuat rangkuman dari hasil percakapan.
          - close   : Untuk menutup percakapan.
        ` });
        this.input = "";
        return;
      }
      this.messages.push({ from: "user", text: userMsg });
      this.input = "";
      this.botLoading = true;
      this.fetchReplyFromAPI(userMsg);
    },
    autoResize(e) {
      const el = e.target

      el.style.height = 'auto'
      el.style.height = el.scrollHeight + 'px'
    },
    async fetchReplyFromAPI(userMsg) {
      try {
        const payload = {
          question: userMsg,
          summary: this.summary
        };
        let newMessage = { from: "bot", text: "", query: null }

        const { data } = await this.$store.dispatch("ai/saveChat", payload);
        newMessage.query = data.query ?? '';
        const botIndex = this.messages.push(newMessage) - 1;
        // this.messages.push({
        //   from: "bot",
        //   text: JSON.stringify(data) ?? "(balasan kosong dari server)"
        // });
        const reply = JSON.stringify(data.data) ?? "<span class='text-red-500 text-xs italic ring-black/5 ring-1 shadow-sm p-2 rounded-sm bg-red-100'>Tidak ada balasan dari server.</span>"
        // await this.typeText(botIndex, reply);
        this.messages[botIndex].text = reply;

        this.scrollToBottom()
        this.botLoading = false;
      } catch (err) {
        this.messages.push({
          from: "bot",
          text: err?.response?.data?.message ? "<span class='text-red-500 text-xs italic ring-black/5 ring-1 shadow-sm p-2 m-2 rounded-sm bg-red-100'>" + err.response.data.message + "</span>" : "<span class='text-red-500 text-xs italic ring-black/5 ring-1 shadow-sm p-2 rounded-sm bg-red-100'>Permintaan tidak dapat diproses.</span>"
        });
        this.scrollToBottom()
        this.botLoading = false;
      }
    },
    tryParseJSON(text) {
      try {
        return JSON.parse(text);
      } catch {
        return null;
      }
    },
    renderMarkdown(text) {
      try {
        let cleaned = this.cleanMarkdownBlock(this.formatNewline(text));

        const parsed = this.tryParseJSON(cleaned);

        if (parsed) {
          return marked('\n' + this.jsonToMarkdownTable(parsed));
        }

        return marked('\n' + cleaned);

      } catch (err) {
        console.error(err);
        return text;
      }
    },
    beautifyHeader(key) {
      return key
        .replace(/_/g, ' ')
        .replace(/\./g, ' ') // kalau hasil flatten ada titik
        .replace(/\b\w/g, c => c.toUpperCase()); // optional: kapital awal
    },
    flatten(obj, prefix = '') {
      return Object.keys(obj).reduce((acc, k) => {
        const pre = prefix ? prefix + '.' : '';

        if (typeof obj[k] === 'object' && obj[k] !== null && !Array.isArray(obj[k])) {
          Object.assign(acc, this.flatten(obj[k], pre + k));
        } else {
          acc[pre + k] = obj[k];
        }

        return acc;
      }, {});
    },

    formatValue(v) {
      if (v === null || v === undefined) return '';

      // auto format date ISO
      if (typeof v === 'string' && /^\d{4}-\d{2}-\d{2}T/.test(v)) {
        return new Date(v).toLocaleString('id-ID');
      }

      if (typeof v === 'boolean') return v ? 'Ya' : 'Tidak';

      if (typeof v === 'object') return JSON.stringify(v);

      return String(v);
    },

    jsonToMarkdownTable(data) {
      if (!Array.isArray(data)) data = [data];

      const flat = data.map(d => this.flatten(d));

      // ambil semua key unik (dinamis)
      const headers = [...new Set(flat.flatMap(o => Object.keys(o)))];

      const headerRow = `| ${headers.map(h => this.beautifyHeader(h)).join(' | ')} |`;
      const sep = `| ${headers.map(() => '---').join(' | ')} |`;

      const rows = flat.map(row =>
        `| ${headers.map(h => this.formatValue(row[h])).join(' | ')} |`
      );

      return [headerRow, sep, ...rows].join('\n');
    },
    formatNewline(str) {
      if (!str) return "";
      return str.replace(/\\n/g, "\n");
    },
    cleanMarkdownBlock(str) {
      return str
        .replace(/```markdown/g, "")
        .replace(/```/g, "");
    },
    typeText(index, text) {
      return new Promise((resolve) => {
        let i = 0;
        const interval = setInterval(() => {
          // langsung assign ke reactive array
          this.messages[index].text = text.slice(0, i + 1);
          i++;
          if (i >= text.length) {
            clearInterval(interval);
            resolve();
          }
        }, 0.5);
      });
    }

  },
  mounted() {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (SpeechRecognition) {
      this.speechSupported = true;
      this.recognition = new SpeechRecognition();
      this.recognition.lang = 'id-ID';
      this.recognition.continuous = true;
      this.recognition.interimResults = false;

      this.recognition.onresult = (event) => {
        // ambil hasil terakhir dari event.results
        const lastIndex = event.results.length - 1;
        const transcript = event.results[lastIndex][0].transcript.trim();

        if (transcript) {
          this.input = transcript;
          this.sendMessage();
        }
      };

      this.recognition.onerror = (event) => {
        alert('Speech recognition error: ' + event.error);
        this.isRecording = false;
      };

      this.recognition.onend = () => {
        // otomatis restart kalau ingin rekaman terus
        if (this.isRecording) {
          this.recognition.start();
        }
      };
    }
  },
};
</script>

<style scoped>

.no-scrollbar::-webkit-scrollbar {
  display: none;
}

.no-scrollbar {
  -ms-overflow-style: none;  /* IE/Edge */
  scrollbar-width: none;     /* Firefox */
}
.chat-wrapper {
  position: fixed;
  bottom: 20px;
  left: 20px;
  z-index: 9999;
}

/* Toggle button */
.chat-toggle {
  width: 52px;
  height: 52px;
  margin-bottom: 10px;
  border-radius: 50%;
  background: #ec7a00;
  color: #fff;
  border: none;
  cursor: pointer;
  font-size: 22px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.chat-box {
  width: 80vw;
  min-width: 80vw;
  height: 80vh;
  background: #f9fafb;
  /* warna background chat */
  border-radius: 10px;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  /* penting! */
}

.chat-body {
  flex: 1;
  padding: 10px;
  overflow-y: auto;
  background: #f9fafb;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

/* Header */
.chat-header {
  background: #ec7a00;
  color: white;
  padding: 12px;
  font-weight: bold;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.close-btn {
  background: transparent;
  border: none;
  color: white;
  font-size: 20px;
  cursor: pointer;
}

/* Footer */
.chat-footer {
  display: flex;
  padding: 10px;
  gap: 6px;
  border-top: 1px solid #ddd;
}

.chat-footer input {
  flex: 1;
  padding: 8px;
  border-radius: 6px;
  border: 1px solid #ccc;
}

.chat-footer button {
  padding: 8px 12px;
  border: none;
  background: #ec7a00;
  color: white;
  border-radius: 6px;
  cursor: pointer;
}

.chat-message {
  padding: 8px;
  border-radius: 8px;
  font-size: 14px;
  max-width: 95%;
  /* jangan lebih dari 80% chat-body */
  box-sizing: border-box;
  /* padding masuk hitungan lebar */
  word-break: break-word;
  /* pecah kata panjang */
  white-space: pre-line;
  /* respect \n */
  margin-bottom: 4px;
  overflow-wrap: break-word;
  /* aman untuk kata super panjang */
}

.chat-message.user {
  background: #d1e7ff;
  align-self: flex-end;
}

.chat-message.bot {
  background: #e5e7eb;
  align-self: flex-start;
}


/* Toggle switch */
.summary-toggle {
  position: relative;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  user-select: none;
}

.summary-toggle input {
  opacity: 0;
  width: 0;
  height: 0;
}

.summary-toggle .slider {
  width: 38px;
  height: 20px;
  background: #ccc;
  border-radius: 20px;
  position: relative;
  transition: 0.2s;
}

.summary-toggle .slider::before {
  content: "";
  position: absolute;
  width: 16px;
  height: 16px;
  background: white;
  border-radius: 50%;
  top: 2px;
  left: 2px;
  transition: 0.2s;
  box-shadow: 0 0 3px rgba(0, 0, 0, 0.3);
}

.summary-toggle input:checked+.slider {
  background: #22c55e;
}

.summary-toggle input:checked+.slider::before {
  transform: translateX(18px);
}

.summary-toggle .label-text {
  color: white;
  font-size: 13px;
  margin-left: 2px;
}

/* Code / pre di chat bot */
.chat-bot-message pre {
  background: #2d2d2d;
  padding: 8px;
  border-radius: 6px;
  overflow-x: auto;
}

.chat-bot-message code {
  font-family: monospace;
  font-size: 14px;
}

::v-deep table {
  border-collapse: collapse;
  width: 100%;
}

::v-deep th,
::v-deep td {
  border: 1px solid #ccc;
  padding: 6px 10px;
}

::v-deep(.markdown-body table) {
  width: max-content;
}

::v-deep(.markdown-body) {
  word-break: break-word;
}
</style>

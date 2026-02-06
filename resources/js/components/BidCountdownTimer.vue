<template>
    <div class="flex items-center gap-6">
        <div class="justify-items-center">
            <div class="bg-slate-900 rounded-lg px-4 py-3 min-w-20 text-center">
                <div class="text-white text-2xl font-bold">
                    {{ timer.days }}
                </div>
            </div>
            <p class="mt-1 text-muted-foreground">Hari</p>
        </div>

        <span class="text-2xl text-gray-400 mb-7">:</span>

        <div class="justify-items-center">
            <div class="bg-slate-900 rounded-lg px-4 py-3 min-w-20 text-center">
                <div class="text-white text-2xl font-bold">
                    {{ timer.hours }}
                </div>
            </div>
            <p class="mt-1 text-muted-foreground">Jam</p>
        </div>

        <span class="text-2xl text-gray-400 mb-7">:</span>

        <div class="justify-items-center">
            <div class="bg-slate-900 rounded-lg px-4 py-3 min-w-20 text-center">
                <div class="text-white text-2xl font-bold">
                    {{ timer.minutes }}
                </div>
            </div>
            <p class="mt-1 text-muted-foreground">Menit</p>
        </div>

        <span class="text-2xl text-gray-400 mb-7">:</span>

        <div class="justify-items-center">
            <div class="bg-slate-900 rounded-lg px-4 py-3 min-w-20 text-center">
                <div class="text-white text-2xl font-bold">
                    {{ timer.seconds }}
                </div>
            </div>
            <p class="mt-1 text-muted-foreground">Detik</p>
        </div>
    </div>
</template>

<script>
export default {
    name: "BidCountDownTimer",
    props: {
        to_date: {
            type: [String, Date],
            required: false, // boleh kosong saat awal
            default: null,
        },
    },
    data() {
        return {
            intervalId: null,
            timer: {
                days: "00",
                hours: "00",
                minutes: "00",
                seconds: "00",
            },
        };
    },
    mounted() {
        // jika sudah ada to_date saat mounted
        if (this.to_date) this.startCountdown();
    },
    beforeUnmount() {
        clearInterval(this.intervalId);
    },
    watch: {
        to_date: {
            immediate: false,
            handler(newVal) {
                // kosong → reset display + stop timer
                if (!newVal) {
                    clearInterval(this.intervalId);
                    this.timer = {
                        days: "00",
                        hours: "00",
                        minutes: "00",
                        seconds: "00",
                    };
                    return;
                }

                // ada nilai → restart timer
                clearInterval(this.intervalId);
                this.startCountdown();
            },
        },
    },
    methods: {
        normalizeToDate(value) {
            if (value instanceof Date) return value;

            if (typeof value === "string" && value.length === 10) {
                return new Date(`${value}T23:59:59`);
            }

            return new Date(value);
        },

        startCountdown() {
            const date = this.normalizeToDate(this.to_date);
            const target = date.getTime();

            // jika invalid, jangan jalan
            if (isNaN(target)) return;

            this.intervalId = setInterval(() => {
                const now = Date.now();
                let diff = target - now;

                if (diff <= 0) {
                    diff = 0;
                    clearInterval(this.intervalId);
                }

                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor(
                    (diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
                );
                const minutes = Math.floor(
                    (diff % (1000 * 60 * 60)) / (1000 * 60)
                );
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                this.timer.days = String(days).padStart(2, "0");
                this.timer.hours = String(hours).padStart(2, "0");
                this.timer.minutes = String(minutes).padStart(2, "0");
                this.timer.seconds = String(seconds).padStart(2, "0");
            }, 1000);
        },
    },
};
</script>

<template>
    <div
        class="bg-background border-b border-border fixed left-0 right-0 pt-1 z-40 h-10"
    >
        <div class="flex items-center justify-between px-2 h-full">
            <!-- Menu Bar -->
            <div class="flex items-center">
                <button
                    class="text-xs text-muted-foreground hover:bg-accent px-3 py-1"
                >
                    Panduan
                </button>
            </div>

            <!-- Toolbar Actions -->
            <div class="flex items-center space-x-2">
                <div class="w-px h-6 bg-border"></div>
                <button
                    class="h-7 w-7 p-0 relative hover:bg-accent flex items-center justify-center"
                >
                    <Bell class="w-3 h-3" />
                    <span
                        class="absolute -top-1 -right-1 w-2 h-2 bg-destructive rounded-full"
                    ></span>
                </button>

                <!-- Profile dropdown -->
                <div class="relative">
                    <button
                        @click="showProfileDropdown = !showProfileDropdown"
                        class="h-8 w-8 flex items-center justify-center rounded-full border border-border text-xs font-semibold hover:bg-accent"
                    >
                        {{ userInitials }}
                    </button>

                    <div
                        v-if="showProfileDropdown"
                        class="absolute right-0 mt-1 w-58 bg-popover border border-border rounded-md shadow-lg z-50"
                    >
                        <div class="px-4 py-3 border-b border-border">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="h-8 w-8 flex items-center justify-center rounded-full border border-border bg-primary text-secondary text-xs font-semibold hover:bg-accent"
                                >
                                    {{ userInitials }}
                                </div>
                                <div>
                                    <p
                                        class="text-sm font-medium text-foreground"
                                    >
                                        {{ user.username }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ user.email }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="py-1">
                            <button
                                @click="handleProfileClick"
                                class="flex items-center w-full px-4 py-2 text-sm text-foreground hover:bg-accent"
                            >
                                <User class="w-4 h-4 mr-3" />
                                Profil
                            </button>
                            <div class="border-t border-border my-1"></div>
                            <button
                                @click="handleLogout"
                                class="flex items-center w-full px-4 py-2 text-sm text-destructive hover:bg-destructive/10"
                            >
                                <LogOut class="w-4 h-4 mr-3" />
                                Keluar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ChangeLog Dialog -->
                <div
                    v-if="showChangeLog"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                    @click="showChangeLog = false"
                >
                    <Card
                        class="w-full max-w-2xl bg-background rounded-md overflow-hidden"
                        @click.stop
                    >
                        <CardContent class="p-2">
                            <ChangeLog />
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>

        <div
            v-if="showProfileDropdown"
            @click="showProfileDropdown = false"
            class="fixed inset-0 z-40"
        ></div>
    </div>
</template>

<script>
import { Bell, User, LogOut } from "lucide-vue-next";
import { mapGetters, mapActions } from "vuex";
import ChangeLog from "@/components/ChangeLog.vue";
import { Card, CardContent } from "@/components/ui/card";

export default {
    name: "TopBar",
    components: { Bell, User, LogOut, ChangeLog, Card, CardContent },

    data() {
        return {
            showProfileDropdown: false,
            searchQuery: "",
            showChangeLog: false,
        };
    },

    computed: {
        ...mapGetters("auth", ["getUser"]),
        user() {
            return this.getUser;
        },
        userInitials() {
            if (!this.user?.name) return "?";

            return this.user.name
                .trim()
                .split(/\s+/)
                .slice(0, 2)
                .map((word) => word[0])
                .join("")
                .toUpperCase();
        },
    },

    methods: {
        ...mapActions("auth", ["logout"]),

        handleProfileClick() {
            console.log("[v0] Profile clicked");
            this.showProfileDropdown = false;
            // Tambahkan navigasi profil jika diperlukan
        },

        handleLogout() {
            if (confirm("Apakah Anda yakin ingin logout?")) {
                this.showProfileDropdown = false;
                this.logout().then(() => this.$router.push("/login"));
            }
        },

        handleClickOutside(event) {
            if (!event.target.closest(".relative")) {
                this.showProfileDropdown = false;
            }
        },
    },

    mounted() {
        document.addEventListener("click", this.handleClickOutside);
    },

    beforeUnmount() {
        document.removeEventListener("click", this.handleClickOutside);
    },
};
</script>

<script setup>
import { Head } from "@inertiajs/vue3";
import { BiPaypal, RiSecurePaymentFill } from "oh-vue-icons/icons";

import VueIcon from "@/Components/VueIcon.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import GatewaySettings from "@/Pages/Admin/Settings/Payments/GatewaySettings.vue";
defineProps({
    gateways: Object,
});
</script>
<template>
    <Head title="Payment settings" />
    <AdminLayout>
        <div class="container py-8">
            <div class="grid sm:grid-cols-2">
                <div>
                    <div class="flex items-center space-x-1">
                        <VueIcon
                            :icon="RiSecurePaymentFill"
                            class="w-6 h-6 text-emerald-500"
                        />
                        <h1
                            class="text-2xl font-semibold text-gray-900 dark:text-gray-100"
                        >
                            Payment Gateways config
                        </h1>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Configure payment gateways
                    </p>
                </div>
            </div>
            <div class="grid gap-5 mt-8">
                <div class="card mt-8">
                    <div class="card-body p-6 grid gap-4">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <VueIcon
                                    :icon="BiPaypal"
                                    class="mr-2 h-6 w-6 text-emerald-600 dark:text-emerald-400"
                                />
                                <h3
                                    class="text-lg !text-sky-600 dark:!text-sky-400"
                                >
                                    Manage Gateways
                                </h3>
                            </div>
                        </div>
                        <div class="grid sm:grid-cols-2 gap-6 sm:gap-10">
                            <GatewaySettings
                                v-for="(config, gateway) in gateways"
                                :key="gateway"
                                :gateway="gateway"
                                :config="config"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<!-- Dashboard.vue -->

<script setup>
import VueIcon from "@/Components/VueIcon.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminAccounts from "@/Pages/Admin/Dashboard/AdminAccounts.vue";
import StatsCard from "@/Pages/Admin/Dashboard/StatsCard.vue";
import {
    ArrowUpIcon,
    CreditCardIcon,
    ShoppingCartIcon,
    UsersIcon,
} from "@heroicons/vue/24/solid";
import { OiHome } from "oh-vue-icons/icons";
import { uid } from "uid";
import { ref } from "vue";
import AccountsCharts from "./Dashboard/AccountsCharts.vue";
const statistics = ref([
    { uid: uid(), name: "House Wins", stat: "$3000" },
    { uid: uid(), name: "Arbitrage", stat: "$1490" },
    { uid: uid(), name: "Losses", stat: "$690" },
    { uid: uid(), name: "Profit", stat: "$2187" },
    { uid: uid(), name: "Bookie", stat: "$13.97K" },
    { uid: uid(), name: "Exchange", stat: "$31.36K" },
]);
</script>
<template>
    <AdminLayout>
        <div class="container py-8">
            <div class="grid sm:grid-cols-2">
                <div>
                    <div class="flex items-center space-x-1">
                        <VueIcon
                            :icon="OiHome"
                            class="w-6 h-6 text-emerald-500"
                        />
                        <h1
                            class="text-2xl font-semibold text-gray-900 dark:text-gray-100"
                        >
                            Dashboard
                        </h1>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Welcome, admin! You have 8 new registrations today.
                    </p>
                </div>
                <div class="flex justify-end items-center">
                    <div class="px-2 sm:px-5 text-center">
                        <p class="text-2xl text-dark">6,584</p>
                        <p class="text-gray-400">Active Jobs</p>
                    </div>
                    <div
                        class="px-2 sm:px-5 text-center border-l-2 border-gray-300 dark:border-gray-600"
                    >
                        <p class="text-2xl text-dark">2,960</p>
                        <p class="text-gray-400">Freelancers</p>
                    </div>
                    <div
                        class="px-2 sm:px-5 text-center border-l-2 border-gray-300 dark:border-gray-600"
                    >
                        <p class="text-2xl text-dark">980</p>
                        <p class="text-gray-400">Companies</p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <StatsCard
                    :icon="UsersIcon"
                    iconBackgroundColor="bg-blue-500"
                    :value="2388"
                    label="Registered Users"
                    :change="19.2"
                    link="#"
                    linkText="View all users"
                />
                <StatsCard
                    :icon="ArrowUpIcon"
                    iconBackgroundColor="bg-green-500"
                    value="14.6%"
                    label="Bounce Rate"
                    :change="-2.3"
                    link="#"
                    linkText="Explore analytics"
                />
                <StatsCard
                    :icon="ShoppingCartIcon"
                    iconBackgroundColor="bg-yellow-500"
                    :value="386"
                    label="Confirmed Sales"
                    :change="7.9"
                    link="#"
                    linkText="View all sales"
                />
                <StatsCard
                    :icon="CreditCardIcon"
                    iconBackgroundColor="bg-purple-500"
                    value="$4,920"
                    label="Total Earnings"
                    :change="-0.3"
                    link="#"
                    linkText="Withdrawal options"
                />
            </div>
            <div
                class="grid rounde-sm mt-8 sm:grid-cols-3 md:grid-cols-6 bg-white p-8 dark:bg-gray-800 transition-all w-full h-full"
            >
                <div
                    v-for="stat in statistics"
                    :key="stat.uid"
                    class="text-center"
                >
                    <div
                        class="uppercase text-gray-800 dark:text-gray-100 tracking-widest text-lg"
                    >
                        {{ stat.name }}
                    </div>
                    <div
                        class="font-inter font-bold text-2xl text-emerald-600 dark:text-emerald-300"
                    >
                        {{ stat.stat }}
                    </div>
                </div>
            </div>
            <AccountsCharts class="mt-4" />
            <AdminAccounts class="mt-4" />
        </div>
    </AdminLayout>
</template>

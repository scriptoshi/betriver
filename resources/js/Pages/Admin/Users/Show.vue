<!-- Dashboard.vue -->

<script setup>
	import { useForm } from "@inertiajs/vue3";
	import { CheckCircleIcon } from "lucide-vue-next";
	import { HiUser } from "oh-vue-icons/icons";

	import Loading from "@/Components/Loading.vue";
	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";
	import Balance from "@/Pages/Admin/Users/Show/Balance.vue";
	import TwoFactorAuth from "@/Pages/Admin/Users/Show/TwoFactorAuth.vue";
	import UserDetails from "@/Pages/Admin/Users/Show/UserDetails.vue";
	const props = defineProps({
		user: Object,
		direct: Number,
		downline: Number,
		personal: Number,
		statistics: Object,
		stats: Object,
	});
	const verifyForm = useForm({
		type: null,
	});
	const verify = (type) => {
		verifyForm.type = type;
		verifyForm.put(window.route("admin.users.verify", props.user), {
			preserveScroll: true,
			preserveState: true,
		});
	};
</script>
<template>
	<AdminLayout>
		<div class="container py-8">
			<div class="grid sm:grid-cols-2">
				<div>
					<div class="flex items-center space-x-1">
						<VueIcon
							:icon="HiUser"
							class="w-6 h-6 text-emerald-500" />
						<h1
							class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
							{{ user.name }}
						</h1>
					</div>
					<p class="text-gray-600 dark:text-gray-300 mb-4">
						{{ user.email }}| Joined
						<i>{{ user.created_at }}</i>
					</p>
				</div>
				<div class="flex justify-end items-center">
					<div class="px-2 sm:px-5 text-center">
						<p class="text-2xl text-dark">
							{{ user.levelConfig.name }}
						</p>
						<p class="text-gray-400">Current Level</p>
					</div>
					<div
						class="px-2 sm:px-5 text-center border-l-2 border-gray-300 dark:border-gray-600">
						<p class="text-2xl text-dark">
							{{ direct }}
						</p>
						<p class="text-gray-400">Referrals</p>
					</div>
					<div
						class="px-2 sm:px-5 text-center border-l-2 border-gray-300 dark:border-gray-600">
						<p class="text-2xl text-dark">{{ downline }}</p>
						<p class="text-gray-400">Downline</p>
					</div>
				</div>
			</div>
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
				<div
					v-for="(stat, name) in stats"
					:key="name"
					class="bg-white shadow-sm text-center dark:bg-gray-800 rounded-lg border dark:border-gray-600">
					<div class="p-6">
						<h3
							class="text-3xl font-inter font-extrabold text-gray-700 dark:text-gray-200">
							<MoneyFormat :amount="stat" />
						</h3>
						<p class="text-gray-500 dark:text-gray-400">
							{{ name }}
						</p>
					</div>
				</div>
			</div>
			<div
				class="grid rounde-sm mt-8 sm:grid-cols-3 md:grid-cols-6 bg-white p-8 dark:bg-gray-800 transition-all w-full">
				<div
					v-for="(stat, name) in statistics"
					:key="name"
					class="text-center">
					<div
						class="uppercase text-gray-800 dark:text-gray-100 tracking-widest text-lg">
						{{ name }}
					</div>
					<div
						class="font-inter font-bold text-2xl text-emerald-600 dark:text-emerald-300">
						<MoneyFormat :amount="stat" />
					</div>
				</div>
			</div>
			<div class="grid gap-5 mt-6 sm:grid-cols-2">
				<UserDetails :user="user" />
				<div>
					<Balance :user="user" />
					<TwoFactorAuth :user="user" />
				</div>
			</div>
			<div class="grid gap-5 mt-6 sm:grid-cols-2">
				<div
					class="p-6 border border-gray-250 dark:border-gray-650 rounded">
					<div class="flex mb-3 justify-between items-center">
						<h3 class="mb-3">ID Card Upload</h3>
						<template v-if="personal.proof_of_identity">
							<CheckCircleIcon
								v-if="user.isKycVerified"
								class="w-8 h-8 text-green-600 dark:text-green-400" />
							<PrimaryButton
								secondary
								v-else
								@click="verify('identity')"
								:disabled="
									verifyForm.processing &&
									verifyForm.type == 'identity'
								">
								<Loading
									class="mr-2 -ml-1"
									v-if="
										verifyForm.processing &&
										verifyForm.type == 'identity'
									" />
								Verify
							</PrimaryButton>
						</template>
					</div>
					<img
						v-if="personal.proof_of_identity"
						class="max-w-md h-auto max-h-[360px]"
						:src="personal.proof_of_identity" />
					<p class="!text-red-500" v-else>ID Card NOT Uploaded</p>
				</div>
				<div
					class="p-6 border border-gray-250 dark:border-gray-650 rounded">
					<div class="flex justify-between items-center">
						<h3 class="mb-3">Address Doc Upload</h3>
						<template v-if="personal.proof_of_address">
							<CheckCircleIcon
								v-if="user.isAddressVerified"
								class="w-8 h-8 text-green-600 dark:text-green-400" />
							<PrimaryButton
								secondary
								v-else
								@click="verify('address')"
								:disabled="
									verifyForm.processing &&
									verifyForm.type == 'address'
								">
								<Loading
									class="mr-2 -ml-1"
									v-if="
										verifyForm.processing &&
										verifyForm.type == 'address'
									" />
								Verify
							</PrimaryButton>
						</template>
					</div>
					<img
						class="max-w-md h-auto max-h-[360px]"
						v-if="personal.proof_of_address"
						:src="personal.proof_of_address" />
					<p class="!text-red-500" v-else>Address Doc NOT Uploaded</p>
				</div>
			</div>
		</div>
	</AdminLayout>
</template>

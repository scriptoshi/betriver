<script setup>
	import { useForm } from "@inertiajs/vue3";

	import FormInput from "@/Components/FormInput.vue";
	import Loading from "@/Components/Loading.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import Switch from "@/Components/Switch.vue";

	const props = defineProps({
		user: Object,
	});

	const form = useForm({
		name: props.user.name,
		email: props.user.email,
		phone: props.user.phone,
		isBanned: props.user.isBanned,
		isKycVerified: props.user.isKycVerified,
		isPhoneVerified: props.user.isPhoneVerified,
		isEmailVerified: props.user.isEmailVerified,
		isAddressVerified: props.user.isAddressVerified,
	});

	const save = () => {
		form.put(window.route("admin.users.update", { user: props.user.id }), {
			preserveScroll: true,
			preserveState: true,
		});
	};
</script>
<template>
	<div class="grid gap-4 p-6 border dark:border-gray-600 rounded-sm">
		<h3
			class="text-xl flex items-center text-emerald-500 dark:text-emerald-400 mb-4">
			Update Details
		</h3>
		<FormInput
			label="User Name"
			:error="form.errors.name"
			v-model="form.name" />
		<FormInput
			label="Email"
			:error="form.errors.email"
			v-model="form.email" />
		<FormInput
			label="Phone"
			:error="form.errors.phone"
			v-model="form.phone" />
		<Switch v-model="form.isBanned">Banned</Switch>
		<Switch v-model="form.isEmailVerified">Email verified</Switch>
		<Switch v-model="form.isKycVerified">Kyc verified</Switch>
		<Switch v-model="form.isPhoneVerified">Phone verified</Switch>
		<Switch v-model="form.isAddressVerified">Address verified</Switch>

		<div class="flex items-end justify-end">
			<PrimaryButton
				:disabled="form.processing"
				@click="save"
				class="mt-4"
				primary>
				<Loading class="mr-2 -ml-1" v-if="form.processing" />
				Update User Account
			</PrimaryButton>
		</div>
	</div>
</template>

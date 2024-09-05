<script setup>
	import { useForm } from "@inertiajs/vue3";

	import FormInput from "@/Components/FormInput.vue";
	import Loading from "@/Components/Loading.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import RadioSelect from "@/Components/RadioSelect.vue";

	const props = defineProps({
		user: Object,
	});

	const form = useForm({
		amount: 0,
		details: "",
		type: "credit",
	});

	const save = () => {
		form.put(
			window.route("admin.users.transact", { user: props.user.id }),
			{
				preserveScroll: true,
				preserveState: true,
				onFinish() {
					form.reset();
				},
			},
		);
	};
</script>
<template>
	<div class="grid gap-4 p-6 border dark:border-gray-600 rounded-sm">
		<h3
			class="text-xl flex items-center text-emerald-500 dark:text-emerald-400 mb-4">
			Balance Transaction
		</h3>
		<FormInput
			label="Amount"
			:error="form.errors.amount"
			v-model="form.amount" />
		<FormInput
			label="Details"
			:error="form.errors.details"
			v-model="form.details" />
		<RadioSelect
			:grid="2"
			v-model="form.type"
			:options="[
				{ label: 'Credit', value: 'credit' },
				{ label: 'Debit', value: 'debit' },
			]" />

		<div class="flex items-end justify-end">
			<PrimaryButton
				:disabled="form.processing"
				@click="save"
				class="mt-4"
				primary>
				<Loading class="mr-2 -ml-1" v-if="form.processing" />
				Transact
			</PrimaryButton>
		</div>
	</div>
</template>

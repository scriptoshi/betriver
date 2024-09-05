<script setup>
	import { useForm } from "@inertiajs/vue3";

	import Loading from "@/Components/Loading.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";

	const props = defineProps({
		user: Object,
	});

	const form = useForm({});

	const save = () => {
		form.delete(
			window.route("admin.users.twofactor", { user: props.user.id }),
			{
				preserveScroll: true,
				preserveState: true,
			},
		);
	};
</script>
<template>
	<div class="grid gap-4 mt-5 p-6 border dark:border-gray-600 rounded-sm">
		<h3
			class="text-xl flex items-center text-emerald-500 dark:text-emerald-400">
			Clear Two Factor Auth
		</h3>
		<p class="mb-2">
			Use this to help users who lost 2FA Device to regain access
		</p>

		<div class="flex items-end justify-end">
			<PrimaryButton
				:disabled="form.processing"
				@click="save"
				class="mt-4"
				error>
				<Loading class="mr-2 -ml-1" v-if="form.processing" />
				Remove Two Factor Auth
			</PrimaryButton>
		</div>
	</div>
</template>

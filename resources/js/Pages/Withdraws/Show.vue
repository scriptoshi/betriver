<script setup>
	import { onMounted } from "vue";

	import { Link } from "@inertiajs/vue3";
	import { ArrowLeftCircle } from "lucide-vue-next";

	import WeCopy from "@/Components/WeCopy.vue";
	import FrontendLayout from "@/Layouts/FrontendLayout.vue";
	import Transaction from "@/Pages/Withdraws/Show/Transaction.vue";
	const props = defineProps({
		withdraw: Object,
		redirect: String,
	});
	const isValidUrl = (string) => {
		try {
			// eslint-disable-next-line no-new
			new URL(string);
			return true;
		} catch (_) {
			return false;
		}
	};
	onMounted(() => {
		if (props.redirect && isValidUrl(props.redirect))
			setTimeout(() => {
				window.location.href = props.redirect;
			}, 1500);
	});
</script>

<template>
	<FrontendLayout>
		<div class="px-3.5 mb-12">
			<div class="flex space-x-4 items-center">
				<Link
					class="text-gray-400 hover:text-gray-850 transition-colors duration-300 dark:hover:text-white"
					:href="route('withdraws.create')">
					<ArrowLeftCircle class="w-12 h-12 stroke-[1px]" />
				</Link>
				<div class="grid py-6">
					<h1
						class="text-3xl text-gray-650 dark:text-white font-inter font-semibold">
						{{ $t("Withdraw balance") }}
					</h1>
					<WeCopy after :text="withdraw.uuid">
						<p>{{ withdraw.uuid }}</p>
					</WeCopy>
				</div>
			</div>
			<Transaction :withdraw="withdraw" />
		</div>
	</FrontendLayout>
</template>

<script setup>
	import { useForm } from "@inertiajs/vue3";
	import { AlertCircle } from "lucide-vue-next";

	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import Loading from "@/Components/Loading.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import RadioSelect from "@/Components/RadioSelect.vue";
	import Switch from "@/Components/Switch.vue";
	import { Alert, AlertDescription, AlertTitle } from "@/Components/ui/alert";

	defineProps({
		personal: Object,
	});
	const form = useForm({
		days: 1,
		confirm: false,
	});
	const timePeriods = [
		{ value: 1, label: "1 Day" },
		{ value: 7, label: "7 Days" },
		{ value: 31, label: "31 Days" },
		{ value: 42, label: "6 Weeks" },
	];
	const updateGrossLimit = () => {
		form.put(window.route("personal.timeout"), {
			preserveScroll: true,
		});
	};
</script>

<template>
	<div
		class="p-4 sm:p-8 bg-white text-gray-700 dark:text-gray-100 dark:bg-gray-850 sm:rounded">
		<header>
			<h2 class="text-xl font-medium text-gray-900 dark:text-gray-100">
				Time out
			</h2>
			<p class="mb-4">
				A timeout is a responsible gambling feature that allows you to
				take a voluntary break from betting. Here's what you need to
				know:
			</p>

			<ul class="list-disc pl-5 space-y-2">
				<li>
					You can choose to activate a timeout for: 1 day, 7 days, 31
					days, 42 days
				</li>
				<li>
					During your chosen timeout period, you won't be able to
					place any bets or use the website
				</li>
				<li>
					This feature is entirely optional and under your control
				</li>
				<li>
					It's designed to help you manage your betting activity
					responsibly
				</li>
				<li>
					When the timeout expires your account will be automatically
					reopened.
				</li>
			</ul>

			<p class="mt-4 font-medium">
				Remember, taking a break can be a positive step in maintaining a
				healthy relationship with betting.
			</p>
		</header>
		<div class="grid mt-8 gap-6">
			<div>
				<FormLabel class="mb-2">Choose a timeout period</FormLabel>
				<RadioSelect
					v-model="form.days"
					:options="timePeriods"
					class="gap-3 mt-4" />
			</div>
			<div>
				<FormLabel>Confirm</FormLabel>
				<div class="mt-4">
					<Switch v-model="form.confirm">
						I confirm that I want a timeout from betting
					</Switch>
				</div>
			</div>
			<Alert>
				<AlertCircle class="w-4 h-4" />
				<AlertTitle>Please NOTE:</AlertTitle>
				<AlertDescription>
					After you submit the form below you will be locked out of
					your account.
				</AlertDescription>
			</Alert>
		</div>
		<div class="mt-4">
			<CollapseTransition>
				<p
					v-show="form.recentlySuccessful"
					class="mb-3 text-green-500 dark:text-green-400">
					Saved successfully
				</p>
			</CollapseTransition>
			<PrimaryButton
				@click="updateGrossLimit"
				:disabled="form.processing || !form.confirm"
				class="text-xs font-semibold uppercase">
				<Loading v-if="form.processing" class="!w-4 !h-4 mr-2 -ml-1" />
				Initiate Timeout
			</PrimaryButton>
		</div>
	</div>
</template>

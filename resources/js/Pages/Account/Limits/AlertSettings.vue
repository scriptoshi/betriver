<script setup>
	import { useForm } from "@inertiajs/vue3";

	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import FormLabel from "@/Components/FormLabel.vue";
	import Loading from "@/Components/Loading.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import Switch from "@/Components/Switch.vue";

	const props = defineProps({
		personal: Object,
        
	});
	const form = useForm({
		bet_emails: props.personal.bet_emails,
		mailing_list: props.personal.mailing_list,
		confirm_bets: props.personal.confirm_bets,
	});

	const updateGrossLimit = () => {
		form.put(window.route("personal.limit.stake"), {
			preserveScroll: true,
		});
	};
</script>

<template>
	<div class="p-4 sm:p-8 bg-white dark:bg-gray-850 sm:rounded">
		<div class="grid mt-8">
			<FormLabel class="mb-1">Bet Result Emails</FormLabel>
			<Select v-model="form.bet_emails">
				<SelectTrigger class="max-w-xs">
					<SelectValue placeholder="Method of notification" />
				</SelectTrigger>
				<SelectContent>
					<SelectGroup>
						<SelectLabel>Fruits</SelectLabel>
						<SelectItem value="apple">Apple</SelectItem>
						<SelectItem value="banana">Banana</SelectItem>
						<SelectItem value="blueberry">Blueberry</SelectItem>
						<SelectItem value="grapes">Grapes</SelectItem>
						<SelectItem value="pineapple">Pineapple</SelectItem>
					</SelectGroup>
				</SelectContent>
			</Select>
			<div>
				<FormLabel class="mb-2">Mailing List</FormLabel>
				<div class="mb-4">
					<Switch v-model="form.mailing_list">
						I'd like to receive updates and special offers.
					</Switch>
				</div>

				<FormLabel class="mb-2">Skip bet confirmations</FormLabel>
				<div>
					<Switch v-model="form.confirm_bets">
						Tick to place bets immediately without any confirmation.
					</Switch>
				</div>
			</div>
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
				:disabled="form.processing || !personal.canUpdateStake"
				class="text-xs font-semibold uppercase">
				<Loading v-if="form.processing" class="!w-4 !h-4 mr-2 -ml-1" />
				Update Stake Limits
			</PrimaryButton>
		</div>
	</div>
</template>

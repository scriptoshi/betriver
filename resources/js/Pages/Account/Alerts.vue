<script setup>
	import { useForm } from "@inertiajs/vue3";

	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import FormLabel from "@/Components/FormLabel.vue";
	import Loading from "@/Components/Loading.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import Switch from "@/Components/Switch.vue";
	import {
		Select,
		SelectContent,
		SelectGroup,
		SelectItem,
		SelectTrigger,
		SelectValue,
	} from "@/Components/ui/select";
	import SettingsLayout from "@/Pages/Account/Settings/SettingsLayout.vue";

	const props = defineProps({
		personal: Object,
		mailOptions: Object,
	});
	const form = useForm({
		bet_emails: props.personal.bet_emails,
		mailing_list: props.personal.mailing_list,
		confirm_bets: props.personal.confirm_bets,
	});

	const updateGrossLimit = () => {
		form.put(window.route("personal.alerts"), {
			preserveScroll: true,
		});
	};
</script>

<template>
	<SettingsLayout>
		<div class="p-4 sm:p-8 bg-white dark:bg-gray-850 sm:rounded">
			<div class="grid">
				<FormLabel class="mb-1">Bet Result Emails</FormLabel>
				<Select v-model="form.bet_emails">
					<SelectTrigger class="max-w-sm">
						<SelectValue
							class="text-gray-650 font-semibold dark:text-gray-300"
							placeholder="Method of notification" />
					</SelectTrigger>
					<SelectContent>
						<SelectGroup>
							<SelectItem
								v-for="option in mailOptions"
								:key="option.value"
								:value="option.value">
								{{ option.label }}
							</SelectItem>
						</SelectGroup>
					</SelectContent>
				</Select>
				<div class="my-6">
					<FormLabel class="mb-2">Mailing List</FormLabel>
					<div class="mb-4">
						<Switch v-model="form.mailing_list">
							I'd like to receive updates and special offers.
						</Switch>
					</div>

					<FormLabel class="mb-2">Skip bet confirmations</FormLabel>
					<div>
						<Switch v-model="form.confirm_bets">
							Tick to place bets immediately without any
							confirmation.
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
					:disabled="form.processing"
					class="text-xs font-semibold uppercase">
					<Loading
						v-if="form.processing"
						class="!w-4 !h-4 mr-2 -ml-1" />
					Update Notification settings
				</PrimaryButton>
			</div>
		</div>
		<div class="bg-white mt-8 dark:bg-gray-800 p-6 rounded shadow-md">
			<h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">
				Protecting Your Contact Privacy
			</h2>

			<p class="mb-4 text-gray-700 dark:text-gray-300">
				At {{ $page.props.appName }}, we take your privacy seriously.
				Here's how we protect your contact information:
			</p>

			<ul class="space-y-2 text-gray-600 dark:text-gray-400">
				<li class="flex items-start">
					<svg
						class="w-6 h-6 mr-2 text-green-500 flex-shrink-0"
						fill="none"
						stroke="currentColor"
						viewBox="0 0 24 24"
						xmlns="http://www.w3.org/2000/svg">
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M5 13l4 4L19 7"></path>
					</svg>
					<span>
						We will never spam you or send unsolicited emails.
					</span>
				</li>
				<li class="flex items-start">
					<svg
						class="w-6 h-6 mr-2 text-green-500 flex-shrink-0"
						fill="none"
						stroke="currentColor"
						viewBox="0 0 24 24"
						xmlns="http://www.w3.org/2000/svg">
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M5 13l4 4L19 7"></path>
					</svg>
					<span>
						Your contact information will never be shared with
						third-party advertisers or spammers.
					</span>
				</li>
				<li class="flex items-start">
					<svg
						class="w-6 h-6 mr-2 text-green-500 flex-shrink-0"
						fill="none"
						stroke="currentColor"
						viewBox="0 0 24 24"
						xmlns="http://www.w3.org/2000/svg">
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M5 13l4 4L19 7"></path>
					</svg>
					<span>
						We use industry-standard encryption to protect your data
						during transmission and storage.
					</span>
				</li>
				<li class="flex items-start">
					<svg
						class="w-6 h-6 mr-2 text-green-500 flex-shrink-0"
						fill="none"
						stroke="currentColor"
						viewBox="0 0 24 24"
						xmlns="http://www.w3.org/2000/svg">
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M5 13l4 4L19 7"></path>
					</svg>
					<span>
						You can opt-out of non-essential communications at any
						time.
					</span>
				</li>
				<li class="flex items-start">
					<svg
						class="w-6 h-6 mr-2 text-green-500 flex-shrink-0"
						fill="none"
						stroke="currentColor"
						viewBox="0 0 24 24"
						xmlns="http://www.w3.org/2000/svg">
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M5 13l4 4L19 7"></path>
					</svg>
					<span>
						We regularly review and update our privacy practices to
						ensure your information remains secure.
					</span>
				</li>
			</ul>

			<p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
				For more details, please review our full
				<a
					href="#"
					class="text-blue-600 hover:underline dark:text-blue-400">
					Privacy Policy
				</a>
				.
			</p>
		</div>
	</SettingsLayout>
</template>

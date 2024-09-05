<script setup>
	import { computed } from "vue";

	import { useForm } from "@inertiajs/vue3";

	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import Loading from "@/Components/Loading.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import {
		Dialog,
		DialogContent,
		DialogDescription,
		DialogFooter,
		DialogHeader,
		DialogTitle,
	} from "@/Components/ui/dialog";
	import { Textarea } from "@/Components/ui/textarea";
	const props = defineProps({
		open: Boolean,
	});
	const emit = defineEmits(["update:open"]);
	const isOpen = computed({
		set: (val) => emit("update:open", val),
		get: () => props.open,
	});

	const form = useForm({
		feedback: "",
	});

	const submitFeedback = () => {
		form.post(window.route("accounts.feedback"), {
			preserveScroll: true,
			preserveState: true,
			onFinish() {
				form.feedback = "";
				setTimeout(function () {
					isOpen.value = false;
				}, 1500);
			},
		});
	};
</script>

<template>
	<Dialog v-model:open="isOpen">
		<DialogContent
			class="sm:max-w-[425px] bg-gray-50 dark:bg-gray-850 text-gray-700 dark:text-white">
			<DialogHeader>
				<DialogTitle>Send us some feedback!</DialogTitle>
				<DialogDescription>
					We value your feedback as we enhance our platform. This
					channel is monitored by our tech team for improvement
					suggestions only. For account or service inquiries, please
					contact Customer Service.
				</DialogDescription>
			</DialogHeader>
			<div class="grid py-4">
				<Textarea
					rows="5"
					class="dark:bg-gray-900 text-base"
					id="feedback"
					:disabled="form.recentlySuccessful || form.processing"
					v-model="form.feedback" />
			</div>
			<DialogFooter class="grid gap-3">
				<CollapseTransition>
					<p
						v-show="form.recentlySuccessful"
						class="mb-3 text-green-500 dark:text-green-400">
						submitted successfully
					</p>
				</CollapseTransition>
				<PrimaryButton
					@click="submitFeedback"
					type="button"
					class="w-full">
					<Loading
						v-if="form.processing"
						class="mr-2 !w-4 !h-4 -ml-1" />
					Submit feedback
				</PrimaryButton>
			</DialogFooter>
		</DialogContent>
	</Dialog>
</template>

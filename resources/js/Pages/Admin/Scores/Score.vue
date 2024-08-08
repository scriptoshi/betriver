<script setup>
	import { useForm } from "@inertiajs/vue3";

	import FormInput from "@/Components/FormInput.vue";
	import Loading from "@/Components/Loading.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	const props = defineProps({
		score: Object,
		game: Object,
	});
	const form = useForm({
		...props.score,
	});

	const save = () => form.post(window.route("admin.scores.store"));
</script>
<template>
	<div class="card border-0 card-border">
		<div class="card-body px-6 card-gutterless h-full">
			<h2 class="text-lg font-semibold text-sky-500">
				{{ score.name }}
			</h2>
			<div class="grid py-4 gap-6 sm:grid-cols-3">
				<FormInput
					:label="game.homeTeam?.name"
					v-model="form.home"
					sm />
				<FormInput :label="game.awayTeam?.name" v-model="form.away" />
				<div class="flex items-end">
					<PrimaryButton
						:disabled="form.processing"
						@click="save"
						class="w-full !py-2"
						secondary>
						<Loading
							class="mr-2 -ml-1 w-5 h-5"
							v-if="form.processing" />
						Save game scores
					</PrimaryButton>
				</div>
			</div>
		</div>
	</div>
</template>

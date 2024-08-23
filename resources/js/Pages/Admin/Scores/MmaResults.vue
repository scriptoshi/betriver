<script setup>
	import { computed } from "vue";

	import { useForm } from "@inertiajs/vue3";
	import { HiSolidChevronDown, HiSolidX } from "oh-vue-icons/icons";

	import FormInput from "@/Components/FormInput.vue";
	import FormLabel from "@/Components/FormLabel.vue";
	import Loading from "@/Components/Loading.vue";
	import Multiselect from "@/Components/Multiselect/Multiselect.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import RadioCards from "@/Components/RadioCards.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import Score from "@/Pages/Admin/Scores/Score.vue";

	const props = defineProps({
		game: Object,
		scores: Object,
	});
	const form = useForm({
		winner: props.game.win_team_id,
		game_id: props.game.id,
		won_type: null,
		round: null,
		minute: null,
		ko_type: null,
		sub_type: null,
		...(props.game.result ?? {}),
	});

	const isKO = computed(() =>
		["KO", "TKO", "DQ", "NC"].includes(form.won_type),
	);
	const isSubmission = computed(() =>
		["SUB", "TSUB"].includes(form.won_type),
	);
	const isDecision = computed(() => ["Points"].includes(form.won_type));
	const winType = {
		KO: "Knockout (KO)",
		TKO: "Technical Knockout (TKO)",
		DQ: "Disqualification (DQ)",
		NC: "No Contest (NC)",
		SUB: "Submission (SUB)",
		TSUB: "Technical Submission (TSUB)",
		Points: "Points",
		"S Dec": "Split Decision",
		"U Dec": "Unanimous Decision",
		"M Dec": "Majority Decision",
	};
	const save = () => form.post(window.route("admin.scores.mma"));
	const fighters = [
		{
			value: props.game.home_team_id,
			title: props.game.homeTeam.name,
			subtitle: "MMA FIGHTER",
			img: props.game.homeTeam.image,
		},
		{
			value: props.game.away_team_id,
			title: props.game.awayTeam.name,
			subtitle: "MMA FIGHTER",
			img: props.game.awayTeam.image,
		},
	];
</script>
<template>
	<div class="flex flex-col gap-6">
		<div class="card border-0 card-border">
			<div class="card-body px-6 card-gutterless h-full">
				<div>
					<RadioCards
						v-model="form.winner"
						label="Winner"
						:options="fighters">
						<template #img="{ img }">
							<img v-if="img" :src="img" class="w-14 h-14 mr-3" />
						</template>
					</RadioCards>
				</div>
				<div class="grid py-4 gap-6 sm:grid-cols-5">
					<div class="sm:col-span-2">
						<FormLabel class="mb-2">Win Type</FormLabel>
						<Multiselect
							class="md"
							:options="winType"
							:placeholder="$t('Select Winning Style')"
							v-model="form.won_type"
							searchable
							closeOnSelect>
							<template #caret="{ isOpen }">
								<VueIcon
									:class="{
										'rotate-180': isOpen,
									}"
									class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
									:icon="HiSolidChevronDown" />
							</template>
							<template #clear="{ clear }">
								<VueIcon
									@click="clear"
									class="mr-1 relative z-10 opacity-60 w-5 h-5"
									:icon="HiSolidX" />
							</template>
						</Multiselect>
						<p
							class="mt-1 ml-1 text-xs font-semibold text-gray-600 dark:text-gray-300"
							id="email-error">
							required
						</p>
					</div>
					<FormInput
						size="sm"
						label="Win Round"
						v-model="form.round"
						help="required" />
					<FormInput
						label="End Minute:secs"
						size="sm"
						v-model="form.minute"
						:error="form.errors.minute"
						placeholder="2:37"
						help="required" />
					<FormInput
						label="KO Type"
						v-if="isKO"
						size="sm"
						v-model="form.ko_type"
						:error="form.errors.ko_type"
						placeholder="Punch"
						help="optional" />
					<FormInput
						v-if="isSubmission"
						size="sm"
						label="Submission Type"
						v-model="form.sub_type"
						:error="form.errors.sub_type"
						placeholder="Triangle Choke"
						help="optional" />
				</div>
				<div class="flex items-end justify-end w-full">
					<PrimaryButton
						:disabled="form.processing"
						@click="save"
						class="!py-2"
						primary>
						<Loading
							class="mr-2 -ml-1 w-5 h-5"
							v-if="form.processing" />
						Save Fight Results
					</PrimaryButton>
				</div>
			</div>
		</div>
		<div v-if="isDecision">
			<Score
				v-for="score in scores"
				:key="score.type"
				:score="score"
				:game="game" />
		</div>
	</div>
</template>

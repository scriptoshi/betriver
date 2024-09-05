<script setup>
	import { computed, ref, watch } from "vue";

	import {
		CalendarDate,
		DateFormatter,
		getLocalTimeZone,
		toZoned,
	} from "@internationalized/date";
	import { Calendar as CalendarIcon } from "lucide-vue-next";

	import { Button } from "@/Components/ui/button";
	import {
		Popover,
		PopoverContent,
		PopoverTrigger,
	} from "@/Components/ui/popover";
	import { RangeCalendar } from "@/Components/ui/range-calendar";
	import { cn } from "@/utils";

	const df = new DateFormatter("en-US", {
		dateStyle: "medium",
	});

	/**
	 * Converts a timestamp to a CalendarDate object.
	 * @param {number} timestamp - The timestamp.
	 * @returns {CalendarDate} A CalendarDate object representing the date.
	 */
	function timestampToCalendarDate(timestamp) {
		const date = new Date(timestamp * 1000);
		return new CalendarDate(
			date.getUTCFullYear(),
			date.getUTCMonth() + 1,
			date.getUTCDate(),
		);
	}

	/**
	 * Converts a CalendarDate object to a timestamp.
	 * @param {CalendarDate} calendarDate - The CalendarDate object.
	 * @param {string} timeZone - The time zone to use (e.g., 'UTC', 'America/New_York').
	 * @returns {number} The timestamp .
	 */
	function calendarDateToTimestamp(calendarDate, timeZone = "UTC") {
		console.log(calendarDate);
		const zonedDateTime = toZoned(calendarDate, timeZone);
		return zonedDateTime.toDate().getTime() / 1000;
	}

	const props = defineProps({
		time: { type: [Number, String], default: "custom" },
		from: [Number, String],
		to: [Number, String],
	});
	const time = computed({
		set: (val) => emit("update:time", val),
		get: () => props.time,
	});
	const from = computed({
		set: (val) => emit("update:from", val),
		get: () => props.from,
	});
	const to = computed({
		set: (val) => emit("update:to", val),
		get: () => props.to,
	});
	const emit = defineEmits(["update:time", "update:from", "update:to"]);

	const value = ref({
		start: from.value ? timestampToCalendarDate(from) : null,
		end: to.value ? timestampToCalendarDate(to) : null,
	});

	const setTime = (val) => {
		value.value = {};
		time.value = val;
		from.value = null;
		to.value = null;
	};
	watch(value, ({ start, end }) => {
		if (!start && !end) return;
		time.value = "custom";
		from.value = start ? calendarDateToTimestamp(start) : null;
		to.value = end ? calendarDateToTimestamp(end) : null;
	});

	const times = [
		{ name: "today", value: 0 },
		{ name: "last 24 hours", value: 1 },
		{ name: "last 3 days", value: 3 },
		{ name: "last 7 days", value: 7 },
		{ name: "last 30 days", value: 30 },
		{ name: "last 365 days", value: 365 },
	];
	const selected = computed(() => {
		if (time.value === "custom") return null;
		const t = times.find((t) => t.value === time.value);
		return t?.name?.toUpperCase();
	});
</script>

<template>
	<Popover>
		<PopoverTrigger as-child>
			<Button
				variant="outline"
				:class="
					cn(
						'w-full sm:w-64 justify-start text-left font-normal  !border !border-emerald-500',
						!value && !selected && 'text-muted-foreground',
					)
				">
				<CalendarIcon class="mr-2 h-4 w-4" />
				<span v-if="selected">{{ selected }}</span>
				<template v-else-if="value.start">
					<template v-if="value.end">
						{{ df.format(value.start.toDate(getLocalTimeZone())) }}
						- {{ df.format(value.end.toDate(getLocalTimeZone())) }}
					</template>

					<template v-else>
						{{ df.format(value.start.toDate(getLocalTimeZone())) }}
					</template>
				</template>
				<template v-else>Pick a date</template>
			</Button>
		</PopoverTrigger>
		<PopoverContent side="bottom" align="start" class="w-auto p-0">
			<div class="grid grid-cols-3 gap-3 p-3">
				<Button
					v-for="t in times"
					:key="t.name"
					@click="setTime(t.value)"
					class="font-semibold uppercase text-[10px] rounded-sm"
					size="xs"
					:variant="t.value === time ? 'secondary' : 'outline'">
					{{ t.name }}
				</Button>
			</div>
			<RangeCalendar
				v-model="value"
				initial-focus
				:number-of-months="2"
				@update:start-value="
					(startDate) => (value.start = startDate)
				" />
		</PopoverContent>
	</Popover>
</template>

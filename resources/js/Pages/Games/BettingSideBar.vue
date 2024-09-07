<script setup>
	import BetFormWidget from "@/Components/Cards/BetFormWidget.vue";
	import BookieFormSubmit from "@/Components/Cards/BookieFormSubmit.vue";
	import BookieFormWidget from "@/Components/Cards/BookieFormWidget.vue";
	import { useBookieForm, useExchangeForm } from "@/Pages/Games/bettingForm";

	const { exchangeForm, removeBet } = useExchangeForm();
	const { bookieForm, removeBet: removeBookie } = useBookieForm();
</script>

<template>
	<div>
		<div
			v-if="Object.keys(exchangeForm).length > 0"
			v-show="!$page.props.multiples">
			<div
				class="bg-gray-300 text-gray-900 dark:text-white dark:bg-gray-750 border-b border-gray-250 dark:border-gray-850 flex items-center px-2.5 uppercase font-inter text-sm tracking-[1px] font-bold h-12 box-border flex-shrink-0 flex-wrap m-0">
				{{ $t("Bet Slips") }}
			</div>
			<div class="grid divide-y divide-gray-250 dark:divide-gray-700">
				<BetFormWidget
					v-for="(bet, guid) in exchangeForm"
					:key="guid"
					:bet="bet"
					v-model:price="bet.price"
					v-model:stake="bet.stake"
					@remove="removeBet(bet)" />
			</div>
		</div>
		<div
			v-if="Object.keys(bookieForm).length > 0"
			v-show="$page.props.multiples">
			<div
				class="bg-gray-300 text-gray-900 dark:text-white dark:bg-gray-750 border-b border-gray-250 dark:border-gray-850 flex items-center px-2.5 uppercase font-inter text-sm tracking-[1px] font-bold h-12 box-border flex-shrink-0 flex-wrap m-0">
				{{ $t("Multiple Ticket") }}
			</div>
			<div class="grid divide-y divide-gray-250 dark:divide-gray-700">
				<BookieFormWidget
					v-for="(book, guid) in bookieForm"
					:key="guid"
					:bet="book"
					@remove="removeBookie(book)" />
				<BookieFormSubmit />
			</div>
		</div>
	</div>
</template>

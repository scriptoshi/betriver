<script setup>
	import { computed } from "vue";

	import {
		Award,
		Calendar,
		CreditCard,
		DollarSign,
		PanelBottomOpen,
		Ticket,
		User,
	} from "lucide-vue-next";

	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import { ucfirst } from "@/hooks";
	import LevelChange from "@/Pages/Account/Statement/Transactable/LevelChange.vue";
	import PayoutDetails from "@/Pages/Account/Statement/Transactable/PayoutDetails.vue";
	import StakeDetails from "@/Pages/Account/Statement/Transactable/StakeDetails.vue";
	import TicketDetails from "@/Pages/Account/Statement/Transactable/TicketDetails.vue";
	import WithdrawDepositDetails from "@/Pages/Account/Statement/Transactable/WithdrawDepositDetails.vue";

	const props = defineProps({
		transaction: {
			type: Object,
			required: true,
		},
	});

	const transactionIcon = computed(() => {
		if (props.transaction.isWithdraw) return CreditCard;
		if (props.transaction.isDeposit) return DollarSign;
		if (props.transaction.isStake) return Award;
		if (props.transaction.isTicket) return Ticket;
		if (props.transaction.isPayout) return User;
		if (props.transaction.isLevel) return PanelBottomOpen;
		return Calendar;
	});
</script>

<template>
	<div class="bg-gray-150 dark:bg-gray-800 rounded-lg p-6">
		<div class="flex items-center mb-4">
			<component :is="transactionIcon" class="w-6 h-6 mr-2" />
			<h3 class="text-xl font-semibold text-gray-800 dark:text-white">
				{{ ucfirst(transaction.type.replace("_", " ")) }} Transaction
			</h3>
		</div>
		<div class="mb-4">
			<p class="text-gray-600 dark:text-gray-300">
				<Calendar class="inline mr-2 w-5 h-5" />
				{{ transaction.created_at }}
			</p>
		</div>
		<div class="mb-4">
			<p class="text-gray-600 dark:text-gray-300">
				<span class="font-semibold mr-2">Amount:</span>
				<MoneyFormat
					:class="
						transaction.isCredit
							? 'text-green-600 dark:text-green-400'
							: 'text-red-600 dark:text-red-400'
					"
					:amount="transaction.amount" />
			</p>
			<p class="text-gray-600 dark:text-gray-300">
				<span class="font-semibold">Action:</span>
				{{ transaction.action }}
			</p>
		</div>
		<div class="border-t border-gray-300 dark:border-gray-700 pt-4">
			<h4
				class="font-semibold text-lg mb-2 text-gray-700 dark:text-gray-200">
				Details:
			</h4>

			<LevelChange
				v-if="transaction.isLevel"
				:transaction="transaction" />

			<PayoutDetails
				v-if="transaction.isPayout"
				:transaction="transaction" />
			<StakeDetails
				v-if="transaction.isStake"
				:transaction="transaction" />
			<TicketDetails
				v-if="transaction.isTicket"
				:transaction="transaction" />
			<WithdrawDepositDetails
				v-if="transaction.isWithdraw || transaction.isDeposit"
				:transaction="transaction" />
		</div>
	</div>
</template>

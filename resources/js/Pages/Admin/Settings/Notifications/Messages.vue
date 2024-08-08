<script setup>
import { MdMessageOutlined } from "oh-vue-icons/icons";

import VueIcon from "@/Components/VueIcon.vue";
import MailMessage from "@/Pages/Admin/Settings/Notifications/Mail/Message.vue";
defineProps({
    mail: Object,
    sms: Object,
});
</script>
<template>
    <div class="card mt-8">
        <div class="card-body p-6 grid gap-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <VueIcon
                        :icon="MdMessageOutlined"
                        class="mr-2 h-6 w-6 text-emerald-600 dark:text-emerald-400"
                    />
                    <h3 class="text-lg !text-sky-600 dark:!text-sky-400">
                        Notification Messages
                    </h3>
                </div>
            </div>
            <MailMessage
                title="Deposit Successfull"
                notification="balance_deposit"
                :message="mail.balance_deposit_message"
                :subject="mail.balance_deposit_subject"
                :salutation="mail.balance_deposit_salutation"
                :sms="sms.balance_deposit_message"
            />

            <MailMessage
                class="mt-8"
                title="Withdraw Successfull"
                notification="balance_withdraw"
                :message="mail.balance_withdraw_message"
                :subject="mail.balance_withdraw_subject"
                :salutation="mail.balance_withdraw_salutation"
                :sms="sms.balance_withdraw_message"
            />
            <MailMessage
                class="mt-8"
                title="Bet Lost Message"
                notification="bet_lost"
                :message="mail.bet_lost_message"
                :subject="mail.bet_lost_subject"
                :salutation="mail.bet_lost_salutation"
                :sms="sms.bet_lost_message"
            />
            <MailMessage
                class="mt-8"
                title="Bet Refunded Message"
                notification="bet_refunded"
                :message="mail.bet_refunded_message"
                :subject="mail.bet_refunded_subject"
                :salutation="mail.bet_refunded_salutation"
                :sms="sms.bet_refunded_message"
            />

            <MailMessage
                class="mt-8"
                title="Bet Winner Message"
                notification="bet_won"
                :message="mail.bet_won_message"
                :subject="mail.bet_won_subject"
                :salutation="mail.bet_won_salutation"
                :sms="sms.bet_won_message"
            />

            <MailMessage
                class="mt-8"
                title="KYC Completed"
                notification="kyc_completed"
                :message="mail.kyc_completed_message"
                :subject="mail.kyc_completed_subject"
                :salutation="mail.kyc_completed_salutation"
                :sms="sms.kyc_completed_message"
            />
            <MailMessage
                class="mt-8"
                title="KYC Rejected"
                notification="kyc_denied"
                :message="mail.kyc_denied_message"
                :subject="mail.kyc_denied_subject"
                :salutation="mail.kyc_denied_salutation"
                :sms="sms.kyc_denied_message"
            />
        </div>
    </div>
</template>

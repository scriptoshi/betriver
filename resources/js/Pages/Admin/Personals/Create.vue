<script setup>
import Loading from "@/Components/Loading.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";
import FormInput from "@/Components/FormInput.vue";
import {HiSolidChevronDown} from "oh-vue-icons/icons";
import MultiSelect from "@/Components/MultiSelect/MultiSelect.vue";
import VueIcon from "@/Components/VueIcon.vue";
import FormSwitch from "@/Components/FormSwitch.vue";
import {DatePicker} from "v-calendar";
import {UseTimeAgo} from "@vueuse/components";
import FormLabel from "@/Components/FormLabel.vue";
defineProps({
    title:{required:false,type:String},
});
const form = useForm({
		user_id:"",
		proof_of_identity:"",
		proof_of_identity_type:"",
		proof_of_address:"",
		proof_of_address_type:"",
		bet_emails:"",
		mailing_list:"",
		confirm_bets:"",
		daily_gross_deposit:"",
		weekly_gross_deposit:"",
		monthly_gross_deposit:"",
		loss_limit_interval:"",
		loss_limit:"",
		stake_limit:"",
		time_out_at:"",
		dob:""
});
const save = () => form.post(window.route("personals.create"));
</script>
<template>
	<Head :title="title ?? `New Personal`" />
	<AdminLayout>
        <main class="h-full">
			<div class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">Add New Personal</h3>
						</div>
						<div class="flex flex-col lg:flex-row lg:items-center gap-3">
							<Link
								:href="route('admin.personals.index')"
								class="py-2.5 px-5 mr-2 mb-2 text-sm font-medium text-gray-900 dark:text-gray-300 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
							>
								<ArrowLeftIcon class="w-4 h-4 -ml-2 mr-2 inline-block" />
								{{ $t("Go Back to personals list") }}</Link
							>
						</div>
					</div>
					<div class="card h-full border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<form
        						class="space-y-8 "
        						@submit.prevent="save()">
        												 
<FormInput
    :label="User_id"
	v-model="form.user_id"
	class="col-span-3"
    :type="number"
	:error="form.errors.user_id"
    
    
/>
        
						 
<FormInput
    :label="Proof_of_identity"
	v-model="form.proof_of_identity"
	class="col-span-3"
    :type="text"
	:error="form.errors.proof_of_identity"
    
    
/>
        
						          <div>
			<FormLabel class="mb-1">{{ $t("Proof_of_identity_type") }}</FormLabel>
			<MultiSelect
				:options="[{key:'idcard', value:'idcard',label:'Identity Card'},
					{key:'passport', value:'passport',label:'Passport'},
					{key:'licence', value:'licence',label:'Drivers Licence'}]"
				valueProp="value"
				label="label"
				:placeholder="$t('')"
				v-model="proof_of_identity_type"
				searchable
				closeOnSelect
				object
			>
				<template #caret="{isOpen}">
					<VueIcon
						:class="{'rotate-180': isOpen}"
						class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
						:icon="HiSolidChevronDown"
					/> </template
			></MultiSelect>
		</div>
       
						 
<FormInput
    :label="Proof_of_address"
	v-model="form.proof_of_address"
	class="col-span-3"
    :type="text"
	:error="form.errors.proof_of_address"
    
    
/>
        
						          <div>
			<FormLabel class="mb-1">{{ $t("Proof_of_address_type") }}</FormLabel>
			<MultiSelect
				:options="[{key:'utility_bill', value:'utility_bill',label:'Utility Bill'},
					{key:'bank_statement', value:'bank_statement',label:'Bank Statement'},
					{key:'other', value:'other',label:'other'}]"
				valueProp="value"
				label="label"
				:placeholder="$t('')"
				v-model="proof_of_address_type"
				searchable
				closeOnSelect
				object
			>
				<template #caret="{isOpen}">
					<VueIcon
						:class="{'rotate-180': isOpen}"
						class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
						:icon="HiSolidChevronDown"
					/> </template
			></MultiSelect>
		</div>
       
						          <div>
			<FormLabel class="mb-1">{{ $t("Bet_emails") }}</FormLabel>
			<MultiSelect
				:options="[{key:'summary', value:'summary',label:'Just email me one summary a day'},
					{key:'settle', value:'settle',label:'Email me when a market I bet on settles'},
					{key:'none', value:'none',label:'Please don't email me about settled bets'}]"
				valueProp="value"
				label="label"
				:placeholder="$t('')"
				v-model="bet_emails"
				searchable
				closeOnSelect
				object
			>
				<template #caret="{isOpen}">
					<VueIcon
						:class="{'rotate-180': isOpen}"
						class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
						:icon="HiSolidChevronDown"
					/> </template
			></MultiSelect>
		</div>
       
						         <FormSwitch v-model="form.mailing_list">{{$t('Mailing_list')}}</FormSwitch>
						         <FormSwitch v-model="form.confirm_bets">{{$t('Confirm_bets')}}</FormSwitch>
						 
<FormInput
    :label="Daily_gross_deposit"
	v-model="form.daily_gross_deposit"
	class="col-span-3"
    :type="number"
	:error="form.errors.daily_gross_deposit"
    
    
/>
        
						 
<FormInput
    :label="Weekly_gross_deposit"
	v-model="form.weekly_gross_deposit"
	class="col-span-3"
    :type="number"
	:error="form.errors.weekly_gross_deposit"
    
    
/>
        
						 
<FormInput
    :label="Monthly_gross_deposit"
	v-model="form.monthly_gross_deposit"
	class="col-span-3"
    :type="number"
	:error="form.errors.monthly_gross_deposit"
    
    
/>
        
						          <div>
			<FormLabel class="mb-1">{{ $t("Loss_limit_interval") }}</FormLabel>
			<MultiSelect
				:options="[{key:'daily', value:'daily',label:'Daily'},
					{key:'weekly', value:'weekly',label:'Weekly'},
					{key:'monthly', value:'monthly',label:'Monthly'},
					{key:'yearly', value:'yearly',label:'Yearly'}]"
				valueProp="value"
				label="label"
				:placeholder="$t('')"
				v-model="loss_limit_interval"
				searchable
				closeOnSelect
				object
			>
				<template #caret="{isOpen}">
					<VueIcon
						:class="{'rotate-180': isOpen}"
						class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
						:icon="HiSolidChevronDown"
					/> </template
			></MultiSelect>
		</div>
       
						 
<FormInput
    :label="Loss_limit"
	v-model="form.loss_limit"
	class="col-span-3"
    :type="number"
	:error="form.errors.loss_limit"
    
    
/>
        
						 
<FormInput
    :label="Stake_limit"
	v-model="form.stake_limit"
	class="col-span-3"
    :type="number"
	:error="form.errors.stake_limit"
    
    
/>
        
						         <div>
			<FormLabel class="mb-1">{{ $t("Time_out_at") }}</FormLabel>
			<DatePicker
				v-model="form.time_out_at"
				mode="dateTime"
				is24hr
			>
				<template v-slot="{inputValue, inputEvents}">
					<input
						class="bg-white border-gray-300 text-gray-900 rounded-sm focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white border block w-full focus:outline-none focus:ring-1 appearance-none py-2 text-sm pl-2 pr-2"
						:value="inputValue"
                        
						v-on="inputEvents"
					/>
				</template>
			</DatePicker>
			<p
				v-if="form.errors.time_out_at"
				class="text-red-500"
			>
				{{ form.errors.time_out_at }}
			</p>
			<UseTimeAgo
				v-else
				v-slot="{timeAgo}"
				:time="form.time_out_at"
			>
				<p class="text-sx font-semibold text-emerald-500">
					{{ timeAgo }}
				</p>
			</UseTimeAgo>
		</div>
						         <div>
			<FormLabel class="mb-1">{{ $t("Dob") }}</FormLabel>
			<DatePicker
				v-model="form.dob"
				mode="dateTime"
				is24hr
			>
				<template v-slot="{inputValue, inputEvents}">
					<input
						class="bg-white border-gray-300 text-gray-900 rounded-sm focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white border block w-full focus:outline-none focus:ring-1 appearance-none py-2 text-sm pl-2 pr-2"
						:value="inputValue"
                        
						v-on="inputEvents"
					/>
				</template>
			</DatePicker>
			<p
				v-if="form.errors.dob"
				class="text-red-500"
			>
				{{ form.errors.dob }}
			</p>
			<UseTimeAgo
				v-else
				v-slot="{timeAgo}"
				:time="form.dob"
			>
				<p class="text-sx font-semibold text-emerald-500">
					{{ timeAgo }}
				</p>
			</UseTimeAgo>
		</div>
        						<div class="pt-5">
        							<div class="flex justify-end">
        								<PrimaryButton
                                            secondary
        									as="button"
        									:href="route('admin.personals.index')"
        									type="button"
                                            link
                                        >
        									{{ $t("Cancel") }}
        								</PrimaryButton>
        								<PrimaryButton
        									type="submit"
        									:disabled="form.processing"
        									>
        									<Loading
        										class="mr-2 -ml-1 inline-block w-5 h-5"
        										v-if="form.processing" />
        									<span class="text-sm text-white">
        										{{ $t("Save Personal") }}
        									</span>
        								</PrimaryButton>
        							</div>
        						</div>
        					</form>
						</div>
					</div>
				</div>
			</div>
		</main>
	</AdminLayout>
</template>

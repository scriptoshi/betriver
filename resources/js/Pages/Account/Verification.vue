<script setup>
	import FrontendLayout from "@/Layouts/FrontendLayout.vue";
	import AddressVerify from "@/Pages/Account/Verification/AddressVerify.vue";
	import IdentityVerify from "@/Pages/Account/Verification/IdentityVerify.vue";
	defineProps({
		idTypes: Array,
		addressTypes: Object,
		personal: Object,
	});
</script>

<template>
	<FrontendLayout>
		<div class="px-3.5 mb-12">
			<div class="grid py-6">
				<h1
					class="text-3xl text-gray-650 dark:text-white font-inter font-semibold">
					{{ $t("Kyc Verification") }}
				</h1>
			</div>
			<IdentityVerify :personal="personal" :idTypes="idTypes" />
			<AddressVerify :personal="personal" :addressTypes="addressTypes" />
			<div class="bg-gray-50 mt-12 dark:bg-gray-850 p-4 rounded12">
				<h3
					class="text-base mb-5 underline font-inter text-gray-800 dark:text-white">
					{{ $t("Why is kyc required?") }}
				</h3>

				<ol
					class="list-decimal pl-5 space-y-2 text-gray-700 dark:text-gray-300">
					<li class="text-base font-medium">
						<span
							class="font-semibold text-gray-900 dark:text-white">
							Protecting You and Us:
						</span>
						We're required by law to collect this information to
						prevent financial crimes and protect both you and our
						platform.
					</li>
					<li class="text-base font-medium">
						<span
							class="font-semibold text-gray-900 dark:text-white">
							Keeping Your Account Safe:
						</span>
						By verifying your identity, we can better protect your
						account from unauthorized access and fraudulent
						activities.
					</li>
					<li class="text-base font-medium">
						<span
							class="font-semibold text-gray-900 dark:text-white">
							Enhancing Your Experience:
						</span>
						With accurate information about you, we can provide more
						personalized services and recommendations.
					</li>
					<li class="text-base font-medium">
						<span
							class="font-semibold text-gray-900 dark:text-white">
							Streamlining Future Interactions:
						</span>
						KYC information helps simplify future transactions and
						interactions you may have on our platform.
					</li>
					<li class="text-base font-medium">
						<span
							class="font-semibold text-gray-900 dark:text-white">
							Building Trust Together:
						</span>
						By participating in our KYC process, you're helping us
						create a more secure and trustworthy environment for all
						users.
					</li>
					<li class="text-base font-medium">
						<span
							class="font-semibold text-gray-900 dark:text-white">
							Ensuring Fair Use:
						</span>
						KYC helps us maintain a level playing field by ensuring
						all users are who they claim to be.
					</li>
				</ol>
			</div>
		</div>
	</FrontendLayout>
</template>

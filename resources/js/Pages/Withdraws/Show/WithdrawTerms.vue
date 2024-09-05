<template>
  <div class="max-w-md mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white">Withdrawal Terms</h2>
    <div class="mb-4">
      <div v-for="(term, index) in terms" :key="index" class="flex items-center mb-2">
        <input
          type="radio"
          :id="`term-${index}`"
          name="withdrawalTerm"
          :value="index"
          v-model="selectedTerm"
          class="mr-2 text-blue-600"
        />
        <label :for="`term-${index}`" class="text-sm text-gray-700 dark:text-gray-300">
          {{ term }}
        </label>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const selectedTerm = ref(0);
const accepted = ref(false);

const terms = [
  "I confirm that this withdrawal request is accurate and authorize [Company Name] to process it according to the terms of service.",
  "By confirming, I acknowledge that this withdrawal is final and subject to [Company Name]'s processing times and fees.",
  "I certify that I am the rightful owner of this account and authorize this withdrawal in accordance with [Company Name]'s policies.",
  "I understand that this withdrawal may be subject to review and agree to cooperate with any necessary verification procedures.",
  "I confirm that this withdrawal complies with all applicable laws and [Company Name]'s terms of service."
];

const confirmWithdrawal = () => {
  if (accepted.value) {
    console.log('Withdrawal confirmed with term:', terms[selectedTerm.value]);
    // Add your withdrawal logic here
  }
};
</script>
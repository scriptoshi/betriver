
<template>
  <div class="max-w-2xl mx-auto p-4">
    <h1 class="text-2xl font-bold text-center mb-4">Hedging calculator</h1>
    <div class="bg-gray-900 text-white rounded-lg shadow-lg overflow-hidden">
      <h2 class="bg-emerald-600 text-white text-xl font-bold p-4 text-center">HEDGING CALCULATOR</h2>
      <div class="p-6">
        <div class="flex justify-center space-x-4 mb-6">
          <button
            @click="backingFirst = true"
            :class="[
              'px-4 py-2 rounded transition-colors',
              backingFirst ? 'bg-emerald-700' : 'bg-emerald-600 hover:bg-emerald-500'
            ]"
          >
            Backing First
          </button>
          <button
            @click="backingFirst = false"
            :class="[
              'px-4 py-2 rounded transition-colors',
              !backingFirst ? 'bg-emerald-700' : 'bg-emerald-600 hover:bg-emerald-500'
            ]"
          >
            Laying First
          </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
          <div v-for="(input, index) in inputs" :key="index" class="space-y-1">
            <label :for="input.id" class="block text-sm font-medium text-emerald-400">{{ input.label }}</label>
            <input
              :id="input.id"
              v-model.number="input.value"
              type="number"
              :step="input.step"
              class="w-full bg-gray-800 text-white border-gray-700 rounded px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500"
            >
          </div>
        </div>

        <div class="mb-6">
          <label for="partialTradeOut" class="block text-sm font-medium text-emerald-400 mb-2">Partial Trade-Out</label>
          <input
            id="partialTradeOut"
            v-model.number="partialTradeOut"
            type="range"
            min="0"
            max="100"
            step="1"
            class="w-full"
          >
          <div class="flex justify-between text-sm text-gray-400">
            <span>0%</span>
            <span>50%</span>
            <span>100%</span>
          </div>
        </div>

        <div class="space-y-4">
          <div v-for="(result, index) in results" :key="index" class="flex justify-between items-center">
            <span class="text-sm font-medium text-emerald-400">{{ result.label }}</span>
            <span :class="[
              'text-lg font-bold',
              result.label === 'COMMISSION PAID' ? 'text-red-500' : 'text-emerald-400'
            ]">
              £{{ result.value.toFixed(2) }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const backingFirst = ref(true);
const backStake = ref(21);
const backOdds = ref(3.5);
const layOdds = ref(2.0);
const commission = ref(5);
const partialTradeOut = ref(100);

const inputs = [
  { id: 'backStake', label: 'BACK STAKE', value: backStake, step: '1' },
  { id: 'backOdds', label: 'BACK ODDS', value: backOdds, step: '0.1' },
  { id: 'layOdds', label: 'LAY ODDS', value: layOdds, step: '0.1' },
  { id: 'commission', label: 'COMMISSION', value: commission, step: '1' },
];

const layStake = computed(() => {
  const stake = (backStake.value * backOdds.value) / layOdds.value;
  return stake * (partialTradeOut.value / 100);
});

const guarantee = computed(() => {
  const backWin = backStake.value * (backOdds.value - 1);
  const layLoss = layStake.value * (layOdds.value - 1);
  return (backWin - layLoss) * (partialTradeOut.value / 100);
});

const commissionPaid = computed(() => {
  return (layStake.value * (layOdds.value - 1) * commission.value) / 100;
});

const results = computed(() => [
  { label: 'YOU SHOULD LAY', value: layStake.value },
  { label: 'TO GUARANTEE', value: guarantee.value },
  { label: 'COMMISSION PAID', value: commissionPaid.value },
]);
</script>
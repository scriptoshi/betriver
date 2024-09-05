import { useLocalStorage } from "@vueuse/core";
const bookieForm = useLocalStorage('-bookie-form-', {});
const exchangeForm = useLocalStorage('-exchange-form-', {});
export const useBookieForm = () => {
    const has = (guid) => bookieForm.value[guid] ?? null;
    const addBet = (bet) => {
        console.log('heheheh');
        bookieForm.value[bet.guid] = bet;
    };
    const removeBet = (bet) => {
        delete bookieForm.value[bet.guid];
    };
    return {
        bookieForm,
        addBet,
        removeBet,
        has
    };
};

export const useExchangeForm = () => {
    const has = (guid) => exchangeForm.value[guid] ?? null;
    const addBet = (bet) => {
        exchangeForm.value[bet.guid] = bet;
    };
    const removeBet = (bet) => {
        delete exchangeForm.value[bet.guid];
    };
    return {
        exchangeForm,
        addBet,
        removeBet,
        has
    };
};
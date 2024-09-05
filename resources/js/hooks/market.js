export class Market {
    static calculateBackOverround(marketData) {
        const bestBackOdds = marketData.bets.map(bet =>
            Math.min(...bet.backs.map(back => back.price))
        );
        return this.calculateOverround(bestBackOdds);
    }

    static calculateLayOverround(marketData) {
        const bestLayOdds = marketData.bets.map(bet =>
            Math.max(...bet.lays.map(lay => lay.price))
        );
        return this.calculateOverround(bestLayOdds);
    }

    static calculateOverround(odds) {
        const impliedProbabilities = odds.map(odd => (1 / odd) * 100);
        const overround = impliedProbabilities.reduce((sum, prob) => sum + prob, 0);
        return Number(overround.toFixed(2));
    }

    static getMarketSummary(marketData) {
        const backOverround = this.calculateBackOverround(marketData);
        const layOverround = this.calculateLayOverround(marketData);
        return {
            market_name: marketData.name,
            back_overround: backOverround,
            lay_overround: layOverround,
            spread: this.calculateSpread(backOverround, layOverround)
        };
    }

    static calculateSpread(backOverround, layOverround) {
        return Number((layOverround - backOverround).toFixed(2));
    }
}
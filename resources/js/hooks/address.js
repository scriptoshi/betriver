

// Shortens an Ethereum address
export function shortenAddress(address = '', charsStart = 4, charsEnd = 4) {
    if (address.length < charsStart + charsEnd + 3) return address;
    return ellipseAddressAdd0x(address, charsStart, charsEnd);
}

/**
 * Shorten an address and add 0x to the start if missing
 * @param targetAddress
 * @param charsStart amount of character to shorten (from both ends / in the beginning)
 * @param charsEnd amount of characters to shorten in the end
 * @returns formatted string
 */
function ellipseAddressAdd0x(targetAddress, charsStart = 4, charsEnd = 4) {
    return ellipseMiddle(targetAddress, charsStart + 2, charsEnd);
}

/**
 * Shorten a string with "..." in the middle
 * @param target
 * @param charsStart amount of character to shorten (from both ends / in the beginning)
 * @param charsEnd amount of characters to shorten in the end
 * @returns formatted string
 */
function ellipseMiddle(target, charsStart = 4, charsEnd = 4) {
    return `${target.slice(0, charsStart)}...${target.slice(target.length - charsEnd)}`;
}

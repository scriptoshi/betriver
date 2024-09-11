import { ref } from "vue";
// ucfirst
export const ucfirst = (string) => {
    return string.charAt(0).toUpperCase() + string.slice(1);
};


export const camel = (string) => {
    return string.replace(/-([a-z])/g, function (g) { return g[1].toUpperCase(); });
};

export const kebab = (string) => {
    return string.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
};

export const lcfirst = (string) => {
    return string.charAt(0).toLowerCase() + string.slice(1);
};

export const limit = (string, limit = 100, end = '...') => {
    if (string.length <= limit) {
        return string;
    }
    return string.slice(0, limit) + end;
};

export const lower = (string) => {
    return string.toLowerCase();
};

export const upper = (string) => {
    return string.toUpperCase();
};

export const rtrim = (string, charlist = " \t\n\r\0\x0B") => {
    return string.replace(new RegExp(`[${charlist}]+$`), '');
};

export const slug = (string, separator = '-') => {
    string = string.replace(/[^a-z0-9\-_]/gi, separator);
    string = string.replace(new RegExp(`${separator}{2,}`, 'g'), separator);
    string = string.replace(new RegExp(`^${separator}|${separator}$`, 'g'), '');
    return string.toLowerCase();
};

export const snake = (string, delimiter = '_') => {
    return string.replace(/([a-z])([A-Z])/g, '$1' + delimiter + '$2').toLowerCase();
};

export const trim = (string, charlist = " \t\n\r\0\x0B") => {
    return string.replace(new RegExp(`^[${charlist}]+|[${charlist}]+$`, 'g'), '');
};

export const title = (string) => {
    return string.replace(/(^|\s)([a-z])/g, function (m, p1, p2) { return p1 + p2.toUpperCase(); });
};

export const take = (string, length = 1) => {
    return string.substr(0, length);
};

export const substr = (string, start, length = null) => {
    return string.substr(start, length);
};

export const start = (string, startwith = '') => {
    return `${startwith}${string}`;
};

export const squish = (string, delimiter = ' ') => {
    return string.replace(new RegExp(`${delimiter}{2,}`, 'g'), delimiter);
};


export const useToggle = () => {
    const isOpen = ref(false);
    const open = () => isOpen.value = true;
    const close = () => isOpen.value = false;
    const toggle = () => isOpen.value ? close() : open();
    return {
        isOpen,
        toggle,
        open,
        close
    };
};

export const useBillions = (billion) => {
    let newValue = parseInt(billion) ?? 0;
    if (!newValue) return 0;
    if (newValue < 1000) return newValue;
    const suffixes = ["", "K", "M", "B", "T"];
    let suffixNum = 0;
    while (newValue >= 1000) {
        newValue /= 1000;
        suffixNum++;
    }
    newValue =
        newValue.toString().length > 2
            ? newValue.toPrecision(3)
            : newValue.toPrecision();
    newValue += suffixes[suffixNum];
    return newValue;
};

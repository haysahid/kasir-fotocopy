const formatCurrency = (number) => {
    if (!number) return 0;

    return Intl.NumberFormat("id-ID").format(number);
};

export default formatCurrency;
const formatNumber = (number) => {
  return new Intl.NumberFormat('id-ID', { style: 'decimal', maximumFractionDigits: 2 }).format(number);
}

const formatNumberx = (number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'decimal',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(number);
};

const formatNumberxx = (number) => {
  // Round the number to 2 decimal places
  const roundedNumber = (+number).toFixed(2);

  // Add parentheses for negative numbers
  const formattedNumber = roundedNumber < 0 ? `(${Math.abs(roundedNumber)})` : roundedNumber;

  // Format with thousands separator (optional)
  const finalFormattedNumber = formattedNumber.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

  return finalFormattedNumber;
};

const toUpper = (str) => {
  return str.toUpperCase();
};

function roundToTwoDecimals(num) {
  return parseFloat(num.toFixed(2));
}


function formatTanggalIndonesia(tanggalISO) {
  const tanggalInput = new Date(tanggalISO);
  const options = {
    year: 'numeric',
    month: 'long', // 'long' untuk nama bulan penuh (Juli), 'short' untuk singkatan (Jul)
    day: 'numeric',
  };

  const tanggalFormatted = tanggalInput.toLocaleDateString('id-ID', options);
  return tanggalFormatted;
};

export default {
  install: (app) => {
    app.provide('formatNumber', formatNumber);
    app.provide('formatNumberx', formatNumberx);
    app.provide('formatNumberxx', formatNumberxx);
    app.provide('toUpper', toUpper);
    app.provide('roundToTwoDecimals', roundToTwoDecimals);
    app.provide('formatTanggalIndonesia', formatTanggalIndonesia);
  }
}
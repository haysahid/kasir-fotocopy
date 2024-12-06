
const getReadableFileSize = function (bytes) {
    if (bytes === 0) {
        return "0.00 B";
    }

    let e = Math.floor(Math.log(bytes) / Math.log(1024));
    return (bytes / Math.pow(1024, e)).toFixed(2) +
        ' ' + ' KMGTP'.charAt(e) + 'B';
}

export default {
    install: (app) => {
        app.provide('getReadableFileSize', getReadableFileSize)
    }
}
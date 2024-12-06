const formatDate = (
    datetime,
    options = {
        dateStyle: "short",
        timeStyle: "short",
    }
) => {
    if (!datetime) return '-';

    options = {
        timeZone: "Asia/Tokyo",
        ...options,
    };

    return Intl.DateTimeFormat("id-ID", options).format(Date.parse(datetime));
};

const formatISODate = (datetime, split = true) => {
    if (!datetime) return '-';

    return split
        ? new Date(datetime).toISOString().split('T')[0]
        : new Date(datetime).toISOString();
};

export default {
    install: (app) => {
        app.provide('formatDate', formatDate);
        app.provide('formatISODate', formatISODate);
    }
}
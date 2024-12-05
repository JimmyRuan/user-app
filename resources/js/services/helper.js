import moment from 'moment';

export function formatDate(dateString, format = "MMMM Do YYYY") {
    if (!dateString) {
        return "Invalid date";
    }

    return moment(dateString).format(format);
}

export default {
    formatDate,
};

import _ from "lodash";
import moment from "moment";

export default {
    data() {
        return {
            freq_types: ["day", "week", "month", "year"],
            multiplications: [1, 7, 30, 365],
            working_days: ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"]
        }
    },
    methods: {
        async callApi(method, url, dataObj) {
            try {
                return await axios({
                    method: method,
                    url: url,
                    data: dataObj
                });

            } catch (error) {
                return error.response
            }
        },
        info(desc, title = "Hey") {
            this.$Notice.info({
                title: title,
                desc: desc
            });
        },
        success(desc, title = "Great") {
            this.$Notice.success({
                title: title,
                desc: desc
            });
        },
        warning(desc, title = "Ooops!") {
            this.$Notice.warning({
                title: title,
                desc: desc
            });
        },
        error(desc, title = "Ooops") {
            this.$Notice.error({
                title: title,
                desc: desc
            });
        },
        smtgWentWrong(desc = 'Something went wrong! Please try again.', title = "Ooops") {
            this.$Notice.error({
                title: title,
                desc: desc
            });
        },
        convertDate(date) {
            return moment(date).format('YYYY-MM-DD');
        },
        convertDateToDays(start_date, end_date) {
            return Math.ceil((end_date.getTime() - start_date.getTime()) / (1000 * 3600 * 24));
        },
        countFreqPerType(start_date, end_date, workday_start, workday_end, freq, type) {
            //days = array of working days you are looking: 0= sunday,.. 6 = saturday
            var days = [];
            var start_index = _.indexOf(this.working_days, workday_start);//1
            var end_index = _.indexOf(this.working_days, workday_end);//5
            if (type == "day") {
                for (let i = start_index; i <= end_index; i++) {
                    days.push(i); //1,2,3,4,5
                }
                var ndays = 1 + Math.round((end_date - start_date) / (24 * 3600 * 1000)); //31
                var sum = function (a, b) {
                    console.log(a, b);
                    return a + Math.floor((ndays + (start_date.getDay() + 6 - b) % 7) / 7);
                };
                return days.reduce(sum, 0) / freq;

            }
            else if (type == "week") {
                days.push(start_date.getDay()) //1
                var ndays = 1 + Math.round((end_date - start_date) / (24 * 3600 * 1000)); //31
                var sum = function (a, b) {
                    console.log(a, b);
                    return a + Math.floor((ndays + (start_date.getDay() + 6 - b) % 7) / 7);
                }; //0 + Math.floor((31 + 0) / 7);
                return days.reduce(sum, 0) / freq;
            }
            else if (type == "month") {
                var months;
                months = (end_date.getFullYear() - start_date.getFullYear()) * 12;
                months -= start_date.getMonth();
                months += end_date.getMonth();
                if (end_date.getDate() >= start_date.getDate()) {
                    months += 1;
                }
                months /= freq;
                return months <= 0 ? 1 : months;
            }
            else {
                var diff = (end_date.getTime() - start_date.getTime()) / 1000;
                diff /= (60 * 60 * 24);
                console.log(diff / 365.25);
                if ((end_date.getDate() >= start_date.getDate()) && (end_date.getMonth() >= start_date.getMonth())) {
                    return (Math.abs(Math.round(diff / 365.25)) + 1) / freq;
                }
                return Math.abs(Math.round(diff / 365.25)) / freq;
            }

        },
        calculateFrequency(freq, type, total_days) {

            return total_days / (freq * this.multiplications[_.indexOf(this.freq_types, type)]);
        },
        calculateFrequency(freq, type, total_days) {
            return total_days / (freq * this.multiplications[_.indexOf(this.freq_types, type)]);
        },
        convertFreqToDays(freq, type) {
            return (freq * this.multiplications[_.indexOf(this.freq_types, type)]);
        }
    }
}
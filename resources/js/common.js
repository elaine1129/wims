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
        getArrayOfWorkingDays(start_index, end_index) {
            var days = [];

            for (let i = start_index; i <= end_index; i++) {
                days.push(i); //1,2,3,4,5
            }
            return days;
        },
        countFreqPerType(start_date, end_date, workday_start, workday_end, freq, type) {
            //days = array of working days you are looking: 0= sunday,.. 6 = saturday
            var start_index = _.indexOf(this.working_days, workday_start);//1
            var end_index = _.indexOf(this.working_days, workday_end);//5
            if (type == "day") {
                var days = this.getArrayOfWorkingDays(start_index, end_index);

                var ndays = 1 + Math.round((end_date - start_date) / (24 * 3600 * 1000)); //31
                var sum = function (a, b) {
                    return a + Math.floor((ndays + (start_date.getDay() + 6 - b) % 7) / 7);
                };
                return days.reduce(sum, 0) / freq;

            }
            else if (type == "week") {
                var days = this.getArrayOfWorkingDays(start_index, end_index);
                //find the first day of the date range according to working days
                var first_day = _.find(days, (day) => {
                    if (start_index < day[0]) { //1<2
                        return day > start_index;
                    } else if (start_index == days[0]) {
                        return start_index;
                    } else {
                        return day < start_index;
                    }
                })
                //calculate total days in the date range
                var ndays = 1 + Math.round((end_date - start_date) / (24 * 3600 * 1000)); //31
                //calculate sum of the same working day as the first day in the date range = weekly
                var sum = function (a, b) {
                    return a + Math.floor((ndays + (start_date.getDay() + 6 - b) % 7) / 7);
                };
                return sum(0, first_day) / freq;
            }
            else if (type == "month") {
                var days = this.getArrayOfWorkingDays(start_index, end_index);
                //calculate the number of days betweem start of the date range from the first working day
                var start_day_index = start_date.getDay();
                var days_from_range = _.forEach(days, (day) => {
                    if (start_day_index == day) {
                        return 0;
                    } else {
                        if (start_day_index < days[0]) {
                            return days[0] - start_day_index;
                        }
                        else {
                            7 - (start_day_index - days[0]);
                        }
                    }
                })
                //add the number of days to get the first working day in the date range
                var first_date = moment(start_date).add(days_from_range, 'days')._d;
                //calculate the month between the end date and the first working day = monthly
                var months;
                months = (end_date.getFullYear() - first_date.getFullYear()) * 12;
                months -= first_date.getMonth();
                months += end_date.getMonth();
                if (end_date.getDate() >= first_date.getDate()) { //make sure to consider partial month by adding 1 
                    months += 1;
                }
                months /= freq;
                return months <= 0 ? 1 : months;
            }
            else {
                var days = this.getArrayOfWorkingDays(start_index, end_index);
                var start_day_index = start_date.getDay();
                //get the number of days between the starting of date range to the first working day
                var days_from_range = _.forEach(days, (day) => {
                    if (start_day_index == day) {
                        return 0;
                    } else {
                        if (start_day_index < days[0]) {
                            return days[0] - start_day_index;
                        }
                        else {
                            7 - (start_day_index - days[0]);
                        }
                    }

                })
                //add the days to obtain the first working day to start the calculation of year
                var first_date = moment(start_date).add(days_from_range, 'days')._d;
                var diff = (end_date.getTime() - first_date.getTime()) / 1000;
                diff /= (60 * 60 * 24);
                //consider the partial year by adding one
                if ((end_date.getDate() >= first_date.getDate()) && (end_date.getMonth() >= first_date.getMonth())) {
                    return (Math.abs(Math.round(diff / 365.25)) + 1) / freq;
                }
                return Math.abs(Math.round(diff / 365.25)) / freq;
            }

        },

        convertFreqToDays(freq, type) {
            return (freq * this.multiplications[_.indexOf(this.freq_types, type)]);
        }
    }
}
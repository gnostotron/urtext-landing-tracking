<?php

/**
 * Code to generate the Report page
 *
 *
 * @link       https://urtext.ca
 * @since      1.0.0
 *
 * @package    Urtext_Landing_Tracking
 * @subpackage Urtext_Landing_Tracking/admin/partials
 */

class Urtext_Landing_Tracking_Admin_Display_Report {

    public function __construct() {
    }

    /**
     * Render the Report page.
     *
     * @since    1.0.0
     */
    function show_reports_page() {
        global $wpdb;
        ?>
        <div class="wrap">
            <style>
                #report {
                    width: 100%;
                    height: 100%;
                }
                #report_filters{
                    margin-top: 20px;
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: flex-start;
                    align-items: center;
                    gap: 20px;
                }
            </style>
            <script>
                const colors = [
                    "#4eb7b4",
                    "#186b80",
                    "#e03932",
                    "#e4722c",
                    "#efb023",
                    "#aaaaaa"
                ]
                const stats = [
                <?php
                    $wpdb_table = $wpdb->prefix . 'urtext_landing_tracking_sessions';
                    $results = $wpdb->get_results($wpdb->prepare("SELECT request_date, slug, post_id, utm_fields, request_count FROM %i ORDER BY request_date", $wpdb_table), ARRAY_A); //db call ok; no-cache ok
                    while ($row = array_shift($results)) {
                        echo "{ day: '" . esc_js($row['request_date']) . "', slug: '" . esc_js($row['slug']) . "', utm_fields: " . json_encode(unserialize($row['utm_fields'])) . ", request_count: " . intval($row['request_count']) . " }, \n";
                    }
                ?>
                ];

                let chart = null;

                const tracking_codes = <?php echo json_encode( get_option('urtext_landing_tracking_codes', array()) ); ?>;

                function prepareChart() {
                    let tracking_code_dict = {};
                    tracking_codes.forEach(code => {
                        let dict_key = "";
                        for (let [key, value] of Object.entries(code).sort()) {
                            if (key == "title" || key == "date_added" || key == "custom_field_content") {
                                continue;
                            }
                            if (key == "custom_field" && value != "" &&  code.custom_field_content != "") {
                                dict_key += value + "=" + code.custom_field_content + "&";
                            } else {
                                dict_key += key + "=" + value + "&"
                            }
                        };
                        tracking_code_dict[dict_key] = code.title;
                    });

                    for (let i = 0; i < stats.length; i++) {
                        let dict_key = "";
                        for (let [key, value] of Object.entries(stats[i].utm_fields).sort()) {
                            dict_key += key + "=" + value + "&"
                        };
                        let tracking_code_title = "";
                        if (dict_key == "") {
                            tracking_code_title = "(No Tracking Code)";
                        } else if (dict_key in tracking_code_dict) {
                            tracking_code_title = tracking_code_dict[dict_key];
                        } else {
                            tracking_code_title = "(Unknown Tracking Code)";
                        }
                        stats[i].title = tracking_code_title;
                    }
                    console.log(JSON.stringify(tracking_code_dict));

                    let slug_list = [];
                    let today = new Date();

                    let start_date = new Date();
                    start_date.setDate(start_date.getDate() - 13);
                    let earliest_date = new Date();
                    earliest_date.setDate(earliest_date.getDate() - 13);
                    stats.forEach(item => {
                        if (!slug_list.includes(item.slug)) {
                            slug_list.push(item.slug);
                        }
                        if (new Date(item.day + "T00:00:00Z") < earliest_date) {
                            earliest_date = new Date(item.day);
                        }
                    });
                    document.getElementById('report_filter_date_start').min = format_date(earliest_date);
                    document.getElementById('report_filter_date_end').min = format_date(earliest_date);
                    document.getElementById('report_filter_date_start').max = format_date(today);
                    document.getElementById('report_filter_date_end').max = format_date(today);
                    document.getElementById('report_filter_date_start').value = format_date(start_date);
                    document.getElementById('report_filter_date_end').value = format_date(today);

                    let option = document.createElement('option');
                    option.value = "";
                    option.innerText = "Show All";
                    document.getElementById('report_filter_slug').appendChild(option);

                    slug_list.forEach(slug => {
                        const option = document.createElement('option');
                        option.value = slug;
                        option.innerText = slug;
                        document.getElementById('report_filter_slug').appendChild(option);
                    });
                }
                function drawChart() {
                    const start_date = new Date(document.getElementById('report_filter_date_start').value);
                    const end_date = new Date(document.getElementById('report_filter_date_end').value);
                    start_date.setMinutes(start_date.getMinutes() + start_date.getTimezoneOffset());
                    end_date.setMinutes(end_date.getMinutes() + end_date.getTimezoneOffset());

                    const chart_title = "Landings Between " + start_date.toLocaleDateString('default', {month:"long",day:"numeric"}) + " and " + end_date.toLocaleDateString('default', {month:"long",day:"numeric"});

                    let chart_dates = [];
                    let chart_labels = []
                    let end = new Date(document.getElementById('report_filter_date_end').value);
                    end.setMinutes(end.getMinutes() + end.getTimezoneOffset());
                    while (end >= start_date) {
                        chart_dates.push(format_date(end));
                        chart_labels.push(end.toLocaleDateString(Intl.DateTimeFormat().resolvedOptions().locale, {month:"short",day:"numeric"}));
                        end.setDate(end.getDate() - 1);
                    }

                    let chart_data = {};
                    chart_data['(No Tracking Code)'] = {};
                    chart_dates.forEach( date => {
                        chart_data['(No Tracking Code)'][date] = 0;
                    });
                    chart_data['(Unknown Tracking Code)'] = {};
                    chart_dates.forEach(date => {
                        chart_data['(Unknown Tracking Code)'][date] = 0;
                    });
                    tracking_codes.forEach(tracking_code => {
                        chart_data[tracking_code.title] = {};
                        chart_dates.forEach(date => {
                            chart_data[tracking_code.title][date] = 0;
                        });
                    });

                    stats.forEach(item => {
                        let item_date = new Date(item.day + "T00:00:00Z");
                        item_date.setMinutes(item_date.getMinutes() + end_date.getTimezoneOffset());
                        if (item_date >= start_date && item_date <= end_date) {
                            if (document.getElementById('report_filter_slug').value != "" && item.slug != document.getElementById('report_filter_slug').value) {
                                return;
                            }
                            if (typeof chart_data[item.title][item.day] === "undefined") {
                                return;
                            }
                            chart_data[item.title][item.day] += item.request_count;
                        }
                    });

                    let datasets = [];
                    for(let [key, value] of Object.entries(chart_data)) {
                        let cur_dataset = {
                            label: key,
                            backgroundColor: colors[datasets.length % colors.length],
                            data: []
                        }
                        chart_dates.forEach( date => {
                            cur_dataset.data.push(value[date]);
                        });
                        datasets.push(cur_dataset);
                    }
                    if (chart == null) {
                        const ctx = document.getElementById('report');
                        chart = new Chart(ctx, {
                            type: 'bar',
                                data: {
                                    labels: chart_labels,
                                    datasets: datasets,
                                },
                                options: {
                                    responsive: true,
                                    plugins: {                                
                                        legend: {
                                            title: {
                                                display: true,
                                                text: "UTM Tracking Codes",
                                            },
                                            position: 'top',
                                        },
                                        title: {
                                            display: true,
                                            text: chart_title,
                                        }
                                    },
                                    scales: {
                                        y: {
                                            ticks: {
                                                precision: 0,
                                            }
                                        },
                                    }
                                }
                        });
                    } else {
                        chart.options.plugins.title.text = chart_title;
                        chart.data = {
                            labels: chart_labels,
                            datasets: datasets,
                        };
                        chart.update();
                    }
                }
                function format_date(date) {
                    return date.getFullYear() + "-" + String(date.getMonth() + 1).padStart(2,"0") + "-" + String(date.getDate()).padStart(2,"0") 
                }

                function downloadCSV() {
                    let csv = "Date,Slug,Request Count,Tracking Title,UTM Codes\n";
                    stats.forEach(item => {
                        csv += '"' + item.day + '","' + item.slug + '",' + item.request_count + ',"' + item.title + '",';
                        let utm_fields = "";
                        for (let [key, value] of Object.entries(item.utm_fields).sort()) {
                            if (utm_fields != "") {
                                utm_fields += "&";
                            }
                            utm_fields += encodeURIComponent(key) + "=" + encodeURIComponent(value);
                        };
                        csv += '"' + utm_fields + "\"\n";
                    });
	                var download_element = document.createElement('a');
	                const csv_blob = new Blob([csv], { type: 'text/csv'});

                    const csv_url = URL.createObjectURL(csv_blob);
	                download_element.href = csv_url;
	                download_element.id="report_download_element";
	                download_element.target = '_blank';
	                var cur_date = new Date();
	                download_element.download = 'urtext_landing_statistics-' + cur_date.toISOString().replace(/\D/g,'') + '.csv';
	                document.getElementById("report_filters").appendChild(download_element);
	                const clickHandler = () => {
		               setTimeout(() => {
			                URL.revokeObjectURL(csv_url);
			                this.removeEventListener('click', clickHandler);
                            document.getElementById("report_filters").removeChild(document.getElementById("report_download_element"));
		                }, 150);
	                };
	                download_element.addEventListener('click', clickHandler, false);
                    download_element.click();
                }
                
            </script>
            <h1><?php esc_html_e( 'Ur-Text Simple Landing Tracking Reports', 'urtext-landing-tracking' ); ?></h1>
            <div><canvas id='report'></canvas></div>
            <div id='report_details'></div>
            <div id='report_filters'>
                <div>
                    <label for='report_filter_date_start'>Start Date:</label>
                    <input type='date' id='report_filter_date_start'>
                </div>
                <div>
                    <label for='report_filter_date_end'>End Date:</label>
                    <input type='date' id='report_filter_date_end'>
                </div>
                <div>
                    <label for='report_filter_slug'>Filter by Page Slug:</label>
                    <select id='report_filter_slug'>
                    </select>
                </div>
                <div>
                    <input type='button' value='Download Data as .CSV' id='download_button' />
                </div>
            </div>
            <script>
                document.getElementById("report").style.height = (document.body.clientHeight - 120) + "px";
                document.getElementById('report_filter_slug').addEventListener('change', drawChart);
                document.getElementById('report_filter_date_start').addEventListener('change', drawChart);
                document.getElementById('report_filter_date_end').addEventListener('change', drawChart);
                document.getElementById('download_button').addEventListener('click', downloadCSV);
                prepareChart();
                drawChart();
            </script>
        </div>
        <?php
    }
}
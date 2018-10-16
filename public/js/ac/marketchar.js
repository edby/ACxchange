

AmCharts.ready( function() {
    Number.prototype.noExponents = function () {
        var data = String(this).split(/[eE]/);
        if (data.length == 1) return data[0];

        var z = '',
            sign = this < 0 ? '-' : '',
            str = data[0].replace('.', ''),
            mag = Number(data[1]) + 1;

        if (mag < 0) {
            z = sign + '0.';
            while (mag++) z += '0';
            return z + str.replace(/^\-/, '');
        }
        mag -= str.length;
        while (mag--) z += '0';
        return str + x;
    }

    function myValue(value, valueText, valueAxis) {
        return value
    }


    var chartData = [];

    var chart = AmCharts.makeChart( "chartdiv", {
        type: "stock",
        "theme": "light",
        height:'404',
        dataDateFormat: "YYYY-MM-DD HH:NN:SS",
        balloonDateFormat: "YYYY-MM-DD HH:NN:SS",
        numberFormatter: {
            usePrefixes: false,
            precision: -1,
            decimalSeparator: ".",
            thousandsSeparator: " "
        },
        categoryAxesSettings: {
            maxSeries: 0,
            minPeriod: "ss",
            equalSpacing: true,
        },
        dataSets: [ {
            fieldMappings: [ {
                fromField: "oopen",
                toField: "oopen"
            }, {
                fromField: "oclose",
                toField: "oclose"
            }, {
                fromField: "ohigh",
                toField: "ohigh"
            }, {
                fromField: "olow",
                toField: "olow"
            }, {
                fromField: "ovolume",
                toField: "ovolume"
            }, {
                fromField: "close",
                toField: "value"
            }, {
                fromField: "average",
                toField: "average"
            } ],

            color: "rgba(95,204,41,0.4)",//底部矩形颜色设置
            dataProvider: chartData,
            title: " ",
            categoryField: "date"
        } ],
        panels: [ {
            title: "Price(24HR)",
            color:"#dde1e7",
            showCategoryAxis: false,
            marginRight: 80,
            percentHeight: 75,
            valueAxes: [ {
                labelFunction: function (value, valueText, valueAxis) {
                    return value.noExponents();
                },
                gridAlpha: 0.25,
                id: "v1",
                dashLength: 1,
                position: "left",
            }],

            categoryAxis: {
                dashLength: 1,
                gridAlpha: 0.25,
            },

            stockGraphs: [ {
                type: "candlestick",
                id: "g1",
                balloonText: "Open:<b>[[oopen]]</b><br>Low:<b>[[olow]]</b><br>High:<b>[[ohigh]]</b><br>Close:<b>[[oclose]]</b><br>Average:<b>[[average]]</b>",
                openField: "oopen",
                closeField: "oclose",
                highField: "ohigh",
                lowField: "olow",
                valueField: "oclose",
                lineColor: "rgba(95,204,41,0.6)",  //上边绿色边框
                fillColors: "rgba(95,204,41,0.6)", //上边边框颜色
                negativeLineColor: "rgba(220,48,48,0.6)",  //上边红色边框
                negativeFillColors: "rgba(220,48,48,0.6)", // 上边红色填充
                fillAlphas: 1,
                useDataSetColors: false,
                showBalloon: true,
                proCandlesticks: true
            } ],

            stockLegend: {
                markerType: "none",
                color:"#dde1e7",
                markerSize: 0,
                forceWidth: true,
                labelWidth: 0,
                labelText: "",
                periodValueText: "",
                periodValueTextRegular: "[[close]]"
            }
        },

            {
                title: "Volume(24HR)",
                color:"#999ea3",
                percentHeight: 25,
                marginTop: 1,
                showCategoryAxis: true,
                valueAxes: [ {
                    labelFunction: function (value, valueText, valueAxis) {
                        return value.noExponents();
                    },
                    inside: false,
                    precision: 8,
                    position: "right",
                    dashLength: 5
                } ],

                categoryAxis: {
                    dashLength: 5
                },

                stockGraphs: [ {
                    valueField: "ovolume",
                    type: "column",
                    showBalloon: true,
                    fillAlphas: 1
                } ],

                stockLegend: {
                    markerType: "none",
                    color:"#dde1e7",
                    markerSize: 0,
                    periodValueText: "",
                    periodValueTextRegular: "[[value]]"
                }
            }
        ],

        chartScrollbarSettings: {
            enabled: false,
        },

        chartCursorSettings: {
            valueLineEnabled: true,
            valueBalloonsEnabled: true,
            zoomable: false
        }
    });
    var myButton = $("#c1D");
    var updatehistory;

   // var currAbb=$('#trade .market0 .lists-bit ul li.activ-bit').find('div').eq(0).find('span').text().toLowerCase();
    var currency=$('#trade .market0 .manu0 li.act2').text().toLowerCase();

    $(".candleget").on("click", function() {
        clearTimeout(updatehistory);
        var myself = $(this);
        var id = myself.attr('id');
        myButton.removeClass('btn-success btn-danger btn-warning').addClass('btn-default')
        myself.removeClass('btn-success btn-danger btn-default').addClass('btn-warning')
        myButton = myself;
        var currAbb=$('#trade .market0 .lists-bit ul li.activ-bit').find('div').eq(0).find('span').text().toLowerCase();
        var index=$(this).parent('li').index();
         var datumType=11 - parseInt(index);
        var curr=$('#trade .market0 .manu0 li.act2').text().toLowerCase();
        console.log("启动定时器: Line 188");
        updateChart(currAbb,datumType,curr);
        //setTimeout(updateChart('bch','1'), 100);
    });

    chart.addListener("dataUpdated", function (event) {
        chart.zoomOut();
    });

    var updatechart_c = 300;
     updatehistory = setTimeout(updateChart, 10000);
      updateChart('bch','5',currency);

    //var currAbb=$('#trade .market0 .lists-bit ul li.activ-bit').find('div').eq(0).find('span').text().toLowerCase();
    //var datumType=1;

    function updateChart(currAbb,datumType,currency) {

        console.log("updateChar 被执行: Line:205");
        var currAbb2=$('#trade .market0 .lists-bit ul li.activ-bit').find('div').eq(0).find('span').text().toLowerCase();
        var curr=$('#trade .market0 .manu0 li.act2').text().toLowerCase();

        var index2=$('#trade .handl-right ul li.activ0').index();
        var type=11 - parseInt(index2);

        (datumType) ? datumType = datumType : datumType = type;
        (currAbb) ? currAbb = currAbb : currAbb = currAbb2;
        (currency) ? currency = currency : currency = curr;

        $.ajax({
            url:'/trade/charts',
            type:"POST",
            async:false,
            data:{ currAbb:currAbb, datumType:datumType,currency:currency},
            success: function(data)
            {
                var result=data.data;

                console.log(result);

                if(result==''){
                    console.log('图表data为空,清空数据');
                    for(i=0;i<=30;i++){
                        var myDate = new Date();
                        chartData[i] = {
                            date:myDate,
                            oopen: 0,
                            oclose: 0,
                            ohigh: 0,
                            olow: 0,
                            ovolume: 0,
                            average: 0,
                            value: 0
                        };
                    }

                    chart.validateData();
                }else {

                    var i = 0;
                    for (var key in result) {
                        if (!result.hasOwnProperty(key)) continue;
                        var row = result[key];
                        chartData[i] = {
                            date: row.add_time,
                            oopen: row.open,
                            oclose: row.close,
                            ohigh: row.high,
                            olow: row.low,
                            ovolume: row.volume,
                            average: row.average,
                            value: row.average
                        };
                        i++;
                    }
                    chart.validateData();


                }
            },error:function(){
                console.log('请求默认数据失败！');
            }
        });

        // });
        if (updatechart_c >= 1) {
            console.log("244 Line启动定时器");
            updatehistory = setTimeout(updateChart, 10000);
            updatechart_c = updatechart_c - 1;
        }

    }

    $('.market0.marketAsk .lists-bit ul').on("click","li",function(){
        clearTimeout(updatehistory);

        var currAbb=$(this).find('div').eq(0).find('span').text().toLowerCase();
        var index=$('#trade .chart-box .handl-right ul li.activ0').index();
        var datumType=11 - parseInt(index);
        var curr=$('#trade .market0 .manu0 li.act2').text().toLowerCase();

        updateChart(currAbb,datumType,curr);



    })
});
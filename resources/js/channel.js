const formatNum = (val) => val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');

new Chart(document.querySelector('canvas').getContext('2d'), {
  type: 'line',
  data: {
    labels,
    datasets: [{
      label: '觀看數',
      data,
      backgroundColor: 'rgba(54, 162, 235, .2)',
      borderColor: 'rgb(54, 162, 235)',
      borderWidth: 2,
      fill: false,
    }],
  },
  options: {
    legend: {
      display: false,
    },
    maintainAspectRatio: false,
    tooltips: {
      mode: 'index',
      intersect: false,
      callbacks: {
        label: (tooltipItem, data) => `${data.datasets[tooltipItem.datasetIndex].label}: ${formatNum(tooltipItem.yLabel)}`,
      }
    },
    scales:{
      xAxes: [{
        display: false,
      }],
      yAxes: [{ ticks: { callback: formatNum } }],
    },
  },
});

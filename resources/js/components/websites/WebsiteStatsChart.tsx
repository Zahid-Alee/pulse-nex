import {
  Bar,
  BarChart,
  CartesianGrid,
  Cell,
  Legend,
  Line,
  LineChart,
  Pie,
  PieChart,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis,
} from "recharts";

// Helper function to format datetime to Asia/Karachi timezone
const formatToKarachiTime = (datetime) => {
  try {
    const date = new Date(datetime);
    return date.toLocaleTimeString("en-PK", {
      timeZone: "Asia/Karachi",
      hour: "2-digit",
      minute: "2-digit",
      hour12: false,
    });
  } catch (error) {
    console.error('Error formatting datetime to Karachi time:', error);
    return datetime;
  }
};

// Helper function to format full datetime for tooltips
const formatFullKarachiTime = (datetime) => {
  try {
    const date = new Date(datetime);
    return date.toLocaleString("en-PK", {
      timeZone: "Asia/Karachi",
      year: "numeric",
      month: "short",
      day: "2-digit",
      hour: "2-digit",
      minute: "2-digit",
      second: "2-digit",
      hour12: false,
    });
  } catch (error) {
    console.error('Error formatting full datetime to Karachi time:', error);
    return datetime;
  }
};

// Helper function for readable date format
const formatReadableKarachiTime = (datetime) => {
  try {
    const date = new Date(datetime);
    return date.toLocaleString("en-US", {
      timeZone: "Asia/Karachi",
      month: "short",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
      hour12: true,
    });
  } catch (error) {
    console.error('Error formatting readable datetime to Karachi time:', error);
    return datetime;
  }
};

const WebsiteStatsChart = ({ data, uptimePercentage, hideCards = false }) => {
  /** --------- Processed Data with Karachi Timezone --------- */
  const processedData =
    data?.map((item, index) => ({
      ...item,
      time: formatToKarachiTime(item.hour),
      fullTime: formatFullKarachiTime(item.hour),
      readableTime: formatReadableKarachiTime(item.hour),
      originalTime: item.hour,
      downtime_percentage: 100 - item.uptime_percentage,
      index: index + 1,
    })) || [];

  const pieData = [
    { name: "Uptime", value: uptimePercentage, color: "#10B981" },
    { name: "Downtime", value: 100 - uptimePercentage, color: "#EF4444" },
  ];

  /** --------- Enhanced Tooltips with Karachi Time --------- */
  const CustomTooltip = ({ active, payload, label }) => {
    if (!active || !payload?.length) return null;
    
    const data = payload[0].payload;
    
    return (
      <div className="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-3 shadow-lg">
        <div className="space-y-1">
          <p className="font-medium text-gray-900 dark:text-gray-100">
            Time: {data.readableTime} PKT
          </p>
          <p className="text-sm text-gray-600 dark:text-gray-400">
            {data.fullTime}
          </p>
          <div className="border-t border-gray-200 dark:border-gray-600 pt-2 mt-2">
            <p className="text-green-600 dark:text-green-400">
              Uptime: {payload[0].value.toFixed(1)}%
            </p>
            {payload[0].value < 100 && (
              <p className="text-red-600 dark:text-red-400">
                Downtime: {(100 - payload[0].value).toFixed(1)}%
              </p>
            )}
          </div>
        </div>
      </div>
    );
  };

  const PieTooltip = ({ active, payload }) => {
    if (!active || !payload?.length) return null;
    
    return (
      <div className="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-3 shadow-lg">
        <p className="font-medium text-gray-900 dark:text-gray-100">
          {payload[0].name}: {payload[0].value.toFixed(1)}%
        </p>
      </div>
    );
  };

  /** --------- Reusable Card --------- */
  const StatCard = ({ color, value, label, icon }) => (
    <div
      className={`rounded-lg border p-6 bg-gradient-to-r ${color.bg} ${color.border} transition-all duration-200 hover:shadow-md`}
    >
      <div className="flex items-center">
        <div className="flex h-10 w-10 items-center justify-center rounded-full bg-white bg-opacity-20 backdrop-blur">
          {icon}
        </div>
        <div className="ml-4">
          <div className={`text-2xl font-bold ${color.text}`}>{value}</div>
          <div className={`text-sm font-medium ${color.subtext}`}>{label}</div>
        </div>
      </div>
    </div>
  );

  /** --------- Main Component JSX --------- */
  return (
    <div className="space-y-6">
      {/* Timezone Indicator */}
      <div className="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-3">
        <div className="flex items-center">
          <div className="h-2 w-2 bg-blue-500 rounded-full mr-2 animate-pulse"></div>
          <span className="text-sm font-medium text-blue-800 dark:text-blue-200">
            All times are shown in Pakistan Time (Asia/Karachi - PKT)
          </span>
        </div>
      </div>

      {/* Overview Cards */}
      {!hideCards && (
        <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
          <StatCard
            value={`${uptimePercentage.toFixed(1)}%`}
            label="Overall Uptime"
            color={{
              bg: "from-green-50 to-green-100 dark:from-green-900 dark:to-green-800",
              border: "border-green-200 dark:border-green-700",
              text: "text-green-900 dark:text-green-100",
              subtext: "text-green-700 dark:text-green-300",
            }}
            icon={
              <svg className="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
              </svg>
            }
          />
          <StatCard
            value={data?.length || 0}
            label="Data Points"
            color={{
              bg: "from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800",
              border: "border-blue-200 dark:border-blue-700",
              text: "text-blue-900 dark:text-blue-100",
              subtext: "text-blue-700 dark:text-blue-300",
            }}
            icon={
              <svg className="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path fillRule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
              </svg>
            }
          />
          <StatCard
            value={processedData.filter((i) => i.uptime_percentage === 100).length}
            label="Perfect Hours"
            color={{
              bg: "from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800",
              border: "border-purple-200 dark:border-purple-700",
              text: "text-purple-900 dark:text-purple-100",
              subtext: "text-purple-700 dark:text-purple-300",
            }}
            icon={
              <svg className="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
            }
          />
        </div>
      )}

      {/* Charts Grid */}
      <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {/* Line Chart */}
        <ChartWrapper title="Uptime Trend (PKT)">
          <ResponsiveContainer width="100%" height={300}>
            <LineChart data={processedData}>
              <CartesianGrid strokeDasharray="3 3" stroke="#e5e7eb" />
              <XAxis 
                dataKey="time" 
                tick={{ fontSize: 12 }}
                angle={-45}
                textAnchor="end"
                height={60}
              />
              <YAxis 
                domain={[95, 100]} 
                tick={{ fontSize: 12 }}
                label={{ value: 'Uptime %', angle: -90, position: 'insideLeft' }}
              />
              <Tooltip content={<CustomTooltip />} />
              <Line
                type="monotone"
                dataKey="uptime_percentage"
                stroke="#10B981"
                strokeWidth={3}
                dot={{ fill: "#10B981", r: 4 }}
                activeDot={{ r: 6, stroke: "#059669", strokeWidth: 2 }}
              />
            </LineChart>
          </ResponsiveContainer>
        </ChartWrapper>

        {/* Pie Chart */}
        <ChartWrapper title="Uptime Distribution">
          <ResponsiveContainer width="100%" height={300}>
            <PieChart>
              <Pie
                data={pieData}
                label={({ name, value }) => `${name}: ${value.toFixed(1)}%`}
                outerRadius={90}
                innerRadius={40}
                dataKey="value"
                labelLine={false}
              >
                {pieData.map((entry, i) => (
                  <Cell key={i} fill={entry.color} />
                ))}
              </Pie>
              <Tooltip content={<PieTooltip />} />
              <Legend 
                verticalAlign="bottom" 
                iconType="circle" 
                wrapperStyle={{ paddingTop: '20px' }}
              />
            </PieChart>
          </ResponsiveContainer>
        </ChartWrapper>
      </div>

      {/* Bar Chart */}
      <ChartWrapper title="Hourly Performance (PKT)">
        <ResponsiveContainer width="100%" height={350}>
          <BarChart data={processedData} margin={{ bottom: 60 }}>
            <CartesianGrid strokeDasharray="3 3" stroke="#e5e7eb" />
            <XAxis 
              dataKey="time" 
              tick={{ fontSize: 11 }}
              angle={-45}
              textAnchor="end"
              height={80}
            />
            <YAxis 
              domain={[0, 100]} 
              tick={{ fontSize: 12 }}
              label={{ value: 'Percentage %', angle: -90, position: 'insideLeft' }}
            />
            <Tooltip content={<CustomTooltip />} />
            <Legend />
            <Bar 
              dataKey="uptime_percentage" 
              fill="#10B981" 
              name="Uptime %" 
              radius={[2, 2, 0, 0]}
            />
            <Bar 
              dataKey="downtime_percentage" 
              fill="#EF4444" 
              name="Downtime %" 
              radius={[2, 2, 0, 0]}
            />
          </BarChart>
        </ResponsiveContainer>
      </ChartWrapper>

      {/* Status Messages */}
      <ChartWrapper title="Status Summary">
        <div className="space-y-3">
          {uptimePercentage >= 99.9 ? (
            <StatusMessage 
              color="green" 
              text="🎉 Excellent uptime performance! Your website is highly reliable." 
            />
          ) : uptimePercentage >= 95 ? (
            <StatusMessage 
              color="yellow" 
              text="⚠️ Good uptime, but there's room for improvement to reach optimal reliability." 
            />
          ) : (
            <StatusMessage 
              color="red" 
              text="🚨 Uptime needs immediate attention. Consider investigating recent issues." 
            />
          )}
          
          {/* Additional info */}
          <div className="mt-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <p className="text-sm text-gray-600 dark:text-gray-400">
              <span className="font-medium">Data Period:</span> {processedData.length > 0 && (
                <>
                  {formatReadableKarachiTime(processedData[processedData.length - 1].originalTime)} - {formatReadableKarachiTime(processedData[0].originalTime)} PKT
                </>
              )}
            </p>
          </div>
        </div>
      </ChartWrapper>
    </div>
  );
};

/** --------- Helper Components --------- */
const ChartWrapper = ({ title, children }) => (
  <div className="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
    <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">{title}</h3>
    {children}
  </div>
);

const StatusMessage = ({ color, text }) => {
  const colorClasses = {
    green: {
      bg: 'bg-green-50 dark:bg-green-900',
      border: 'border-green-200 dark:border-green-700',
      dot: 'bg-green-500',
      text: 'text-green-800 dark:text-green-200'
    },
    yellow: {
      bg: 'bg-yellow-50 dark:bg-yellow-900',
      border: 'border-yellow-200 dark:border-yellow-700', 
      dot: 'bg-yellow-500',
      text: 'text-yellow-800 dark:text-yellow-200'
    },
    red: {
      bg: 'bg-red-50 dark:bg-red-900',
      border: 'border-red-200 dark:border-red-700',
      dot: 'bg-red-500', 
      text: 'text-red-800 dark:text-red-200'
    }
  };

  const classes = colorClasses[color];

  return (
    <div className={`flex items-center rounded-lg border p-4 ${classes.bg} ${classes.border} transition-all duration-200`}>
      <span className={`mr-3 h-3 w-3 rounded-full ${classes.dot} animate-pulse`} />
      <span className={`font-medium ${classes.text}`}>
        {text}
      </span>
    </div>
  );
};

export default WebsiteStatsChart;
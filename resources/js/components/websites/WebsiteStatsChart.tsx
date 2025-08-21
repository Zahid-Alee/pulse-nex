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

const WebsiteStatsChart = ({ data, uptimePercentage, hideCards = false }) => {
  /** --------- Processed Data --------- */
  const processedData =
    data?.map((item, index) => ({
      ...item,
      time: new Date(item.hour).toLocaleTimeString("en-US", {
        hour: "2-digit",
        minute: "2-digit",
        hour12: false,
      }),
      fullTime: item.hour,
      downtime_percentage: 100 - item.uptime_percentage,
      index: index + 1,
    })) || [];

  const pieData = [
    { name: "Uptime", value: uptimePercentage, color: "#10B981" },
    { name: "Downtime", value: 100 - uptimePercentage, color: "#EF4444" },
  ];

  /** --------- Tooltips --------- */
  const CustomTooltip = ({ active, payload, label }) =>
    active && payload?.length ? (
      <div className="tooltip">
        <p>{`Time: ${label}`}</p>
        <p className="text-green-600">{`Uptime: ${payload[0].value}%`}</p>
        {payload[0].value < 100 && (
          <p className="text-red-600">{`Downtime: ${
            100 - payload[0].value
          }%`}</p>
        )}
      </div>
    ) : null;

  const PieTooltip = ({ active, payload }) =>
    active && payload?.length ? (
      <div className="tooltip">
        <p>{`${payload[0].name}: ${payload[0].value}%`}</p>
      </div>
    ) : null;

  /** --------- Reusable Card --------- */
  const StatCard = ({ color, value, label, icon }) => (
    <div
      className={`rounded-lg border p-6 bg-gradient-to-r ${color.bg} ${color.border}`}
    >
      <div className="flex items-center">
        <div className="flex h-8 w-8 items-center justify-center rounded-full bg-opacity-80">
          {icon}
        </div>
        <div className="ml-4">
          <div className={`text-2xl font-bold ${color.text}`}>{value}</div>
          <div className={`text-sm font-medium ${color.subtext}`}>{label}</div>
        </div>
      </div>
    </div>
  );

  /** --------- JSX --------- */
  return (
    <div className="space-y-6">
      {/* Overview Cards */}
      {!hideCards && (
        <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
          <StatCard
            value={`${uptimePercentage}%`}
            label="Overall Uptime"
            color={{
              bg: "from-green-50 to-green-100 dark:from-green-900 dark:to-green-800",
              border: "border-green-200 dark:border-green-700",
              text: "text-green-900 dark:text-green-100",
              subtext: "text-green-700 dark:text-green-300",
            }}
            icon={<span className="text-white">✔</span>}
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
            icon={<span className="text-white">⏱</span>}
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
            icon={<span className="text-white">★</span>}
          />
        </div>
      )}

      {/* Charts Grid */}
      <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {/* Line Chart */}
        <ChartWrapper title="Uptime Trend">
          <ResponsiveContainer width="100%" height={300}>
            <LineChart data={processedData}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="time" />
              <YAxis domain={[95, 100]} />
              <Tooltip content={<CustomTooltip />} />
              <Line
                type="monotone"
                dataKey="uptime_percentage"
                stroke="#10B981"
                strokeWidth={3}
                dot={{ fill: "#10B981", r: 4 }}
                activeDot={{ r: 6 }}
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
                label={({ name, value }) => `${name}: ${value}%`}
                outerRadius={80}
                dataKey="value"
              >
                {pieData.map((entry, i) => (
                  <Cell key={i} fill={entry.color} />
                ))}
              </Pie>
              <Tooltip content={<PieTooltip />} />
              <Legend verticalAlign="bottom" iconType="circle" />
            </PieChart>
          </ResponsiveContainer>
        </ChartWrapper>
      </div>

      {/* Bar Chart */}
      <ChartWrapper title="Hourly Performance">
        <ResponsiveContainer width="100%" height={300}>
          <BarChart data={processedData}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="time" />
            <YAxis domain={[0, 100]} />
            <Tooltip content={<CustomTooltip />} />
            <Legend />
            <Bar dataKey="uptime_percentage" fill="#10B981" name="Uptime %" />
            <Bar dataKey="downtime_percentage" fill="#EF4444" name="Downtime %" />
          </BarChart>
        </ResponsiveContainer>
      </ChartWrapper>

      {/* Status Messages */}
      <ChartWrapper title="Status Summary">
        {uptimePercentage >= 99.9 ? (
          <StatusMessage color="green" text="Excellent uptime performance!" />
        ) : uptimePercentage >= 95 ? (
          <StatusMessage color="yellow" text="Good uptime, room for improvement" />
        ) : (
          <StatusMessage color="red" text="Uptime needs attention" />
        )}
      </ChartWrapper>
    </div>
  );
};

/** --------- Helper Components --------- */
const ChartWrapper = ({ title, children }) => (
  <div className="rounded-lg border p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
    <h3 className="mb-4 text-lg font-semibold">{title}</h3>
    {children}
  </div>
);

const StatusMessage = ({ color, text }) => (
  <div
    className={`flex items-center rounded-lg border p-3 bg-${color}-50 dark:bg-${color}-900`}
  >
    <span className={`mr-3 h-2 w-2 rounded-full bg-${color}-500`} />
    <span className={`font-medium text-${color}-800 dark:text-${color}-200`}>
      {text}
    </span>
  </div>
);

export default WebsiteStatsChart;

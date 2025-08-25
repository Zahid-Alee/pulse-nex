import WebsiteHistoryTable from '@/components/websites/WebsiteHistoryTable';
import WebsiteStatsChart from '@/components/websites/WebsiteStatsChart';
import AppLayout from '@/layouts/app-layout';
import { Head, usePage } from '@inertiajs/react';

export default function ViewWebsite() {
    const { props } = usePage<{ website: any; stats: any; history: any }>();
    const { website, stats, history } = props;

    console.log('history', history);


    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/websites' },
                { title: website.name, href: `/websites/${website.id}` },
            ]}
        >
            <Head title={website.name} />
            <div className="space-y-6 p-4">
                <WebsiteStatsChart data={stats.hourly_data} uptimePercentage={stats.uptime_percentage} />
                <WebsiteHistoryTable history={history.data} />
            </div>
        </AppLayout>
    );
}

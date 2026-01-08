// resources/js/components/SearchFilter.tsx
import { useState, useEffect } from 'react';
import { router } from '@inertiajs/react';
import { Search, Calendar } from 'lucide-react';

interface SearchFilterProps {
    onSearch?: (keyword: string, dateFrom: string, dateTo: string) => void;
}

export default function SearchFilter({ onSearch }: SearchFilterProps) {
    const [keyword, setKeyword] = useState('');
    const [dateFrom, setDateFrom] = useState('');
    const [dateTo, setDateTo] = useState('');

    // Debounce search
    useEffect(() => {
        const debounce = setTimeout(() => {
            if (onSearch) {
                onSearch(keyword, dateFrom, dateTo);
            } else {
                // Default: reload page dengan query params
                const params = new URLSearchParams();
                if (keyword) params.append('keyword', keyword);
                if (dateFrom) params.append('date_from', dateFrom);
                if (dateTo) params.append('date_to', dateTo);
                
                const queryString = params.toString();
                router.get(
                    window.location.pathname + (queryString ? `?${queryString}` : ''),
                    {},
                    { preserveState: true, preserveScroll: true }
                );
            }
        }, 500);

        return () => clearTimeout(debounce);
    }, [keyword, dateFrom, dateTo]);

    return (
        <div className="mb-4 flex flex-col gap-3 sm:flex-row">
            {/* Search Input */}
            <div className="relative flex-1">
                <Search className="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                <input
                    type="text"
                    value={keyword}
                    onChange={(e) => setKeyword(e.target.value)}
                    placeholder="Search documents..."
                    className="w-full rounded-lg border border-input bg-background py-2.5 pl-10 pr-4 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                />
            </div>

            {/* Date From */}
            <div className="relative sm:w-48">
                <Calendar className="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                <input
                    type="date"
                    value={dateFrom}
                    onChange={(e) => setDateFrom(e.target.value)}
                    placeholder="dd/mm/yyyy"
                    className="w-full rounded-lg border border-input bg-background py-2.5 pl-10 pr-4 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                />
            </div>

            {/* Date To */}
            <div className="flex items-center gap-2">
                <span className="text-sm text-muted-foreground">to</span>
                <div className="relative sm:w-48">
                    <Calendar className="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                    <input
                        type="date"
                        value={dateTo}
                        onChange={(e) => setDateTo(e.target.value)}
                        placeholder="dd/mm/yyyy"
                        className="w-full rounded-lg border border-input bg-background py-2.5 pl-10 pr-4 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                    />
                </div>
            </div>
        </div>
    );
}
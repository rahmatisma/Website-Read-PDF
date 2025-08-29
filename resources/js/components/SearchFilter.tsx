import { CalendarIcon, MagnifyingGlassIcon } from '@heroicons/react/24/outline';

interface SearchFilterProps {
    onSearch?: (query: string) => void;
    onDateChange?: (start: string, end: string) => void;
}

export default function SearchFilter({ onSearch, onDateChange }: SearchFilterProps) {
    return (
        <div className="mb-6 flex items-center justify-between gap-6">
            {/* 🔎 Search */}
            <div className="relative w-1/3">
                <MagnifyingGlassIcon className="absolute top-2.5 left-3 h-5 w-5 text-gray-400" />
                <input
                    type="text"
                    placeholder="Search documents..."
                    className="w-full rounded-full bg-gray-800 py-2 pr-4 pl-10 text-gray-200 placeholder-gray-400 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none"
                    onChange={(e) => onSearch?.(e.target.value)}
                />
            </div>

            {/* 📅 Date Filter */}
            <div className="flex items-center gap-3">
                {/* Start Date */}
                <div className="relative">
                    <CalendarIcon className="absolute top-2.5 left-3 h-5 w-5 text-gray-400" />
                    <input
                        type="date"
                        className="rounded-full bg-gray-800 py-2 pr-4 pl-10 text-gray-200 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none"
                        onChange={(e) => onDateChange?.(e.target.value, '')}
                    />
                </div>

                <span className="text-gray-400">to</span>

                {/* End Date */}
                <div className="relative">
                    <CalendarIcon className="absolute top-2.5 left-3 h-5 w-5 text-gray-400" />
                    <input
                        type="date"
                        className="rounded-full bg-gray-800 py-2 pr-4 pl-10 text-gray-200 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none"
                        onChange={(e) => onDateChange?.('', e.target.value)}
                    />
                </div>
            </div>
        </div>
    );
}

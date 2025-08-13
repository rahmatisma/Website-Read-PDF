import { motion } from 'framer-motion';
import { useEffect, useState } from 'react';

interface TabSwitcherProps {
    tabs: string[];
    defaultTab?: string;
    onChange?: (tab: string) => void;
}

export default function TabSwitcher({ tabs, defaultTab, onChange }: TabSwitcherProps) {
    const [activeTab, setActiveTab] = useState(defaultTab || tabs[0]);

    // 🔹 Sinkronkan jika defaultTab berubah (misal dari perubahan URL)
    useEffect(() => {
        if (defaultTab && defaultTab !== activeTab) {
            setActiveTab(defaultTab);
        }
    }, [defaultTab]);

    const handleTabClick = (tab: string) => {
        setActiveTab(tab);
        onChange?.(tab);
    };

    return (
        <div className="flex items-center justify-center">
            <div className="flex w-fit rounded-full bg-[#5C4A35] p-1">
                {tabs.map((tab) => (
                    <button key={tab} onClick={() => handleTabClick(tab)} className="relative px-4 py-1 font-medium text-white">
                        {activeTab === tab && (
                            <motion.div
                                layoutId="active-pill"
                                className="absolute inset-0 z-0 rounded-full bg-yellow-400"
                                transition={{ type: 'spring', stiffness: 300, damping: 25 }}
                            />
                        )}
                        <span className="relative z-10">{tab}</span>
                    </button>
                ))}
            </div>
        </div>
    );
}

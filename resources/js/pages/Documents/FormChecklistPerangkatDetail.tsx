// FormChecklistPerangkatDetail.tsx - Component untuk menampilkan Indoor & Outdoor Area Checklist
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Activity,
    AlertTriangle,
    CheckCircle2,
    ClipboardCheck,
    HardDrive,
    MapPin,
    Network,
    Package,
    Radio,
    Server,
    Signal,
    Thermometer,
    Wifi,
    Zap,
} from 'lucide-react';

interface QualityParameter {
    quality_parameter?: string;
    parameter?: string;
    standard: string;
    existing: string;
    perbaikan?: string;
    hasil_akhir?: string;
}

interface PengukuranTegangan {
    v_output: string;
    p_n: string;
    p_g: string;
    n_g: string;
}

interface SaranaPenunjangIndoor {
    merk_ups?: string;
    kapasitas_ups?: string;
    pengukuran_tegangan?: PengukuranTegangan[];
    parameter_kualitas?: QualityParameter[];
}

interface PerangkatModem {
    catatan_input_modem?: string;
    bertumpuk?: string;
    lokasi_ruang_lantai_rack?: string;
    parameter_kualitas?: QualityParameter[];
}

interface PerangkatCPE {
    pemilik_perangkat_cpe?: string;
    jenis_perangkat_cpe?: string;
    parameter_kualitas?: QualityParameter[];
}

// ✅ Interface baru untuk Indikator Modem
interface IndicatorParameter {
    standard: string;
    nms_engineer?: string;
    on_site_teknisi?: string;
    perbaikan?: string;
    hasil_akhir?: string;
}

interface IndikatorModem {
    power?: IndicatorParameter;
    '109_dcd_link_wan'?: IndicatorParameter;
}

interface MerekModem {
    td_txd_103?: IndicatorParameter;
    rd_rxd_104?: IndicatorParameter;
    rts_105?: IndicatorParameter;
    cts_106?: IndicatorParameter;
    alarm_led?: IndicatorParameter;
    front_panel_display?: {
        all_stu_modem?: IndicatorParameter;
        tainet_scorpio?: IndicatorParameter;
    };
}

interface ModemFO {
    optical_led_alarm?: IndicatorParameter;
}

interface SignalQualityKOP {
    stu_160?: IndicatorParameter;
    stu_1088_2304?: IndicatorParameter;
    adsl_modem?: IndicatorParameter;
}

interface SignalQualityAVO {
    stu_1088_2304?: IndicatorParameter;
    tainet?: IndicatorParameter;
    adtran_express?: IndicatorParameter;
}

interface IndoorAreaChecklist {
    sarana_penunjang?: SaranaPenunjangIndoor;
    perangkat_modem?: PerangkatModem;
    perangkat_cpe?: PerangkatCPE;
    // ✅ Tambahan interface untuk indikator modem
    indikator_modem?: IndikatorModem;
    merek?: MerekModem;
    modem_fo?: ModemFO;
    lc_signal_quality_checked_by_kop?: SignalQualityKOP;
    lc_signal_quality_checked_by_avo_meter?: SignalQualityAVO;
}

interface SiteOutdoor {
    bs_catuan_sektor?: string;
    jarak_udara_heading?: string;
    latitude?: string;
    longitude?: string;
    potential_obstacle?: string;
    quality_parameter?: QualityParameter[];
}

interface SaranaPenunjangOutdoor {
    type_mounting?: string;
    tinggi_mounting?: string;
    type_penangkal_petir?: string;
    quality_parameter?: QualityParameter[];
}

interface PerangkatAntenna {
    polarisasi?: string;
    altitude?: string;
    lokasi?: string;
    quality_parameter?: QualityParameter[];
}

interface CablingInstallation {
    type_kabel_ifl?: string;
    panjang_kabel_ifl?: string;
    tahanan_short_kabel_ifl?: string;
    quality_parameter?: QualityParameter[];
}

interface OutdoorAreaChecklist {
    site?: SiteOutdoor;
    sarana_penunjang?: SaranaPenunjangOutdoor;
    perangkat_antenna?: PerangkatAntenna;
    cabling_installation?: CablingInstallation;
}

interface FormChecklistPerangkatDetailProps {
    indoorAreaChecklist?: IndoorAreaChecklist;
    outdoorAreaChecklist?: OutdoorAreaChecklist;
}

export default function FormChecklistPerangkatDetail({ indoorAreaChecklist, outdoorAreaChecklist }: FormChecklistPerangkatDetailProps) {
    const DetailItem = ({ label, value }: { label: string; value: string | null | undefined }) => {
        if (!value) return null;
        return (
            <div className="flex flex-col gap-1 break-words">
                <span className="text-sm font-medium text-muted-foreground">{label}</span>
                <span className="text-sm font-medium break-words">{value}</span>
            </div>
        );
    };

    const renderQualityParameterTable = (parameters: QualityParameter[], title?: string) => {
        if (!parameters || parameters.length === 0) return null;

        return (
            <div className="space-y-2">
                {title && <h4 className="font-semibold text-sm">{title}</h4>}
                <div className="overflow-x-auto">
                    <table className="w-full text-sm border-collapse">
                        <thead>
                            <tr className="border-b bg-muted/50">
                                <th className="p-2 text-left font-medium">Parameter</th>
                                <th className="p-2 text-left font-medium">Standard</th>
                                <th className="p-2 text-left font-medium">Existing</th>
                                {parameters.some((p) => p.perbaikan) && <th className="p-2 text-left font-medium">Perbaikan</th>}
                                {parameters.some((p) => p.hasil_akhir) && <th className="p-2 text-left font-medium">Hasil Akhir</th>}
                            </tr>
                        </thead>
                        <tbody>
                            {parameters.map((param, index) => (
                                <tr key={index} className="border-b hover:bg-muted/30">
                                    <td className="p-2 break-words">{param.quality_parameter || param.parameter || '-'}</td>
                                    <td className="p-2 break-words">{param.standard || '-'}</td>
                                    <td className="p-2 break-words">{param.existing || '-'}</td>
                                    {parameters.some((p) => p.perbaikan) && <td className="p-2 break-words">{param.perbaikan || '-'}</td>}
                                    {parameters.some((p) => p.hasil_akhir) && <td className="p-2 break-words">{param.hasil_akhir || '-'}</td>}
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        );
    };

    // ✅ Function untuk render indikator parameter table
    const renderIndicatorTable = (data: Record<string, IndicatorParameter>, title: string) => {
        const entries = Object.entries(data);
        if (entries.length === 0) return null;

        // Check apakah ada kolom opsional
        const hasNmsEngineer = entries.some(([_, val]) => val.nms_engineer);
        const hasOnSiteTeknisi = entries.some(([_, val]) => val.on_site_teknisi);
        const hasPerbaikan = entries.some(([_, val]) => val.perbaikan);
        const hasHasilAkhir = entries.some(([_, val]) => val.hasil_akhir);

        return (
            <div className="space-y-2">
                <h4 className="font-semibold text-sm">{title}</h4>
                <div className="overflow-x-auto">
                    <table className="w-full text-sm border-collapse">
                        <thead>
                            <tr className="border-b bg-muted/50">
                                <th className="p-2 text-left font-medium">Parameter</th>
                                <th className="p-2 text-left font-medium">Standard</th>
                                {hasNmsEngineer && <th className="p-2 text-left font-medium">NMS Engineer</th>}
                                {hasOnSiteTeknisi && <th className="p-2 text-left font-medium">On-Site Teknisi</th>}
                                {hasPerbaikan && <th className="p-2 text-left font-medium">Perbaikan</th>}
                                {hasHasilAkhir && <th className="p-2 text-left font-medium">Hasil Akhir</th>}
                            </tr>
                        </thead>
                        <tbody>
                            {entries.map(([key, value], index) => (
                                <tr key={index} className="border-b hover:bg-muted/30">
                                    <td className="p-2 break-words font-medium">{key.replace(/_/g, ' ').toUpperCase()}</td>
                                    <td className="p-2 break-words">{value.standard || '-'}</td>
                                    {hasNmsEngineer && <td className="p-2 break-words">{value.nms_engineer || '-'}</td>}
                                    {hasOnSiteTeknisi && <td className="p-2 break-words">{value.on_site_teknisi || '-'}</td>}
                                    {hasPerbaikan && <td className="p-2 break-words">{value.perbaikan || '-'}</td>}
                                    {hasHasilAkhir && <td className="p-2 break-words">{value.hasil_akhir || '-'}</td>}
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        );
    };

    return (
        <div className="space-y-6">
            {/* ===== INDOOR AREA CHECKLIST ===== */}
            {indoorAreaChecklist && Object.keys(indoorAreaChecklist).length > 0 && (
                <>
                    <div className="mb-4">
                        <h2 className="text-xl font-bold flex items-center gap-2">
                            <ClipboardCheck className="h-6 w-6 text-emerald-500" />
                            Indoor Area Checklist
                        </h2>
                        <p className="text-sm text-muted-foreground mt-1">Pengecekan perangkat indoor</p>
                    </div>

                    <div className="grid gap-4 md:grid-cols-2">
                        {/* Card Sarana Penunjang Indoor */}
                        {indoorAreaChecklist.sarana_penunjang && (
                            <Card className="md:col-span-2">
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Zap className="h-5 w-5 text-blue-500" />
                                        Sarana Penunjang
                                    </CardTitle>
                                    <CardDescription>UPS dan parameter kualitas lingkungan</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    {/* Info UPS */}
                                    {(indoorAreaChecklist.sarana_penunjang.merk_ups || indoorAreaChecklist.sarana_penunjang.kapasitas_ups) && (
                                        <div className="grid gap-4 md:grid-cols-2">
                                            <DetailItem label="Merk UPS" value={indoorAreaChecklist.sarana_penunjang.merk_ups} />
                                            <DetailItem label="Kapasitas UPS" value={indoorAreaChecklist.sarana_penunjang.kapasitas_ups} />
                                        </div>
                                    )}

                                    {/* Tabel Pengukuran Tegangan */}
                                    {indoorAreaChecklist.sarana_penunjang.pengukuran_tegangan &&
                                        indoorAreaChecklist.sarana_penunjang.pengukuran_tegangan.length > 0 && (
                                            <div>
                                                <h4 className="mb-2 font-semibold text-sm">Pengukuran Tegangan</h4>
                                                <div className="overflow-x-auto">
                                                    <table className="w-full text-sm border-collapse">
                                                        <thead>
                                                            <tr className="border-b bg-muted/50">
                                                                <th className="p-2 text-left font-medium">V. Output</th>
                                                                <th className="p-2 text-center font-medium">P-N</th>
                                                                <th className="p-2 text-center font-medium">P-G</th>
                                                                <th className="p-2 text-center font-medium">N-G</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {indoorAreaChecklist.sarana_penunjang.pengukuran_tegangan.map((tegangan, index) => (
                                                                <tr key={index} className="border-b hover:bg-muted/30">
                                                                    <td className="p-2 font-medium">{tegangan.v_output}</td>
                                                                    <td className="p-2 text-center">{tegangan.p_n || '-'}</td>
                                                                    <td className="p-2 text-center">{tegangan.p_g || '-'}</td>
                                                                    <td className="p-2 text-center">{tegangan.n_g || '-'}</td>
                                                                </tr>
                                                            ))}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        )}

                                    {/* Parameter Kualitas */}
                                    {indoorAreaChecklist.sarana_penunjang.parameter_kualitas &&
                                        renderQualityParameterTable(indoorAreaChecklist.sarana_penunjang.parameter_kualitas, 'Parameter Kualitas')}
                                </CardContent>
                            </Card>
                        )}

                        {/* ✅ Card Indikator Modem - BARU */}
                        {indoorAreaChecklist.indikator_modem && (
                            <Card className="md:col-span-2">
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Activity className="h-5 w-5 text-green-500" />
                                        Indikator Modem
                                    </CardTitle>
                                    <CardDescription>Status LED dan indikator modem</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    {renderIndicatorTable(indoorAreaChecklist.indikator_modem as Record<string, IndicatorParameter>, 'Status Indikator')}
                                </CardContent>
                            </Card>
                        )}

                        {/* ✅ Card Merek Modem - BARU */}
                        {indoorAreaChecklist.merek && (
                            <Card className="md:col-span-2">
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Signal className="h-5 w-5 text-indigo-500" />
                                        Parameter Merek Modem
                                    </CardTitle>
                                    <CardDescription>Pengecekan parameter per merek modem</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    {/* Parameter dasar */}
                                    {Object.keys(indoorAreaChecklist.merek)
                                        .filter((key) => key !== 'front_panel_display')
                                        .map((key) => {
                                            const value = indoorAreaChecklist.merek?.[key as keyof MerekModem];
                                            if (value && typeof value === 'object' && 'standard' in value) {
                                                return renderIndicatorTable({ [key]: value as IndicatorParameter }, key.replace(/_/g, ' ').toUpperCase());
                                            }
                                            return null;
                                        })}

                                    {/* Front Panel Display */}
                                    {indoorAreaChecklist.merek.front_panel_display && (
                                        <div>
                                            <h4 className="mb-2 font-semibold">Front Panel Display</h4>
                                            {indoorAreaChecklist.merek.front_panel_display.all_stu_modem &&
                                                renderIndicatorTable(
                                                    { 'All STU Modem': indoorAreaChecklist.merek.front_panel_display.all_stu_modem },
                                                    'All STU Modem',
                                                )}
                                            {indoorAreaChecklist.merek.front_panel_display.tainet_scorpio &&
                                                renderIndicatorTable(
                                                    { 'Tainet Scorpio': indoorAreaChecklist.merek.front_panel_display.tainet_scorpio },
                                                    'Tainet Scorpio',
                                                )}
                                        </div>
                                    )}
                                </CardContent>
                            </Card>
                        )}

                        {/* ✅ Card Modem FO - BARU */}
                        {indoorAreaChecklist.modem_fo && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Network className="h-5 w-5 text-cyan-500" />
                                        Modem Fiber Optic
                                    </CardTitle>
                                    <CardDescription>Status modem FO</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    {renderIndicatorTable(indoorAreaChecklist.modem_fo as Record<string, IndicatorParameter>, 'Optical LED Alarm')}
                                </CardContent>
                            </Card>
                        )}

                        {/* ✅ Card Signal Quality KOP - BARU */}
                        {indoorAreaChecklist.lc_signal_quality_checked_by_kop && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Signal className="h-5 w-5 text-yellow-500" />
                                        Signal Quality (KOP)
                                    </CardTitle>
                                    <CardDescription>Pengecekan kualitas sinyal dengan KOP</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    {renderIndicatorTable(
                                        indoorAreaChecklist.lc_signal_quality_checked_by_kop as Record<string, IndicatorParameter>,
                                        'Kualitas Sinyal',
                                    )}
                                </CardContent>
                            </Card>
                        )}

                        {/* ✅ Card Signal Quality AVO Meter - BARU */}
                        {indoorAreaChecklist.lc_signal_quality_checked_by_avo_meter && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <AlertTriangle className="h-5 w-5 text-orange-500" />
                                        Signal Quality (AVO Meter)
                                    </CardTitle>
                                    <CardDescription>Pengecekan kualitas sinyal dengan AVO Meter</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    {renderIndicatorTable(
                                        indoorAreaChecklist.lc_signal_quality_checked_by_avo_meter as Record<string, IndicatorParameter>,
                                        'Kualitas Sinyal',
                                    )}
                                </CardContent>
                            </Card>
                        )}

                        {/* Card Perangkat Modem */}
                        {indoorAreaChecklist.perangkat_modem && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Server className="h-5 w-5 text-purple-500" />
                                        Perangkat Modem
                                    </CardTitle>
                                    <CardDescription>Pengecekan modem dan catuan</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-3">
                                        <DetailItem label="Catatan Input Modem" value={indoorAreaChecklist.perangkat_modem.catatan_input_modem} />
                                        <DetailItem label="Bertumpuk" value={indoorAreaChecklist.perangkat_modem.bertumpuk} />
                                        <DetailItem
                                            label="Lokasi (Ruang/Lantai/Rack)"
                                            value={indoorAreaChecklist.perangkat_modem.lokasi_ruang_lantai_rack}
                                        />
                                    </div>

                                    {indoorAreaChecklist.perangkat_modem.parameter_kualitas &&
                                        renderQualityParameterTable(indoorAreaChecklist.perangkat_modem.parameter_kualitas)}
                                </CardContent>
                            </Card>
                        )}

                        {/* Card Perangkat CPE */}
                        {indoorAreaChecklist.perangkat_cpe && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <HardDrive className="h-5 w-5 text-orange-500" />
                                        Perangkat CPE
                                    </CardTitle>
                                    <CardDescription>Customer Premises Equipment</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-3">
                                        <DetailItem label="Pemilik Perangkat" value={indoorAreaChecklist.perangkat_cpe.pemilik_perangkat_cpe} />
                                        <DetailItem label="Jenis Perangkat" value={indoorAreaChecklist.perangkat_cpe.jenis_perangkat_cpe} />
                                    </div>

                                    {indoorAreaChecklist.perangkat_cpe.parameter_kualitas &&
                                        renderQualityParameterTable(indoorAreaChecklist.perangkat_cpe.parameter_kualitas)}
                                </CardContent>
                            </Card>
                        )}
                    </div>
                </>
            )}

            {/* ===== OUTDOOR AREA CHECKLIST ===== */}
            {outdoorAreaChecklist && Object.keys(outdoorAreaChecklist).length > 0 && (
                <>
                    <div className="mb-4 mt-8">
                        <h2 className="text-xl font-bold flex items-center gap-2">
                            <Wifi className="h-6 w-6 text-blue-500" />
                            Outdoor Area Checklist
                        </h2>
                        <p className="text-sm text-muted-foreground mt-1">Pengecekan perangkat outdoor dan antenna</p>
                    </div>

                    <div className="grid gap-4 md:grid-cols-2">
                        {/* Card Site */}
                        {outdoorAreaChecklist.site && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <MapPin className="h-5 w-5 text-red-500" />
                                        Site Information
                                    </CardTitle>
                                    <CardDescription>Data lokasi dan base station</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-3">
                                        <DetailItem label="BS Catuan Sektor" value={outdoorAreaChecklist.site.bs_catuan_sektor} />
                                        <DetailItem label="Jarak Udara / Heading" value={outdoorAreaChecklist.site.jarak_udara_heading} />
                                        <DetailItem label="Latitude" value={outdoorAreaChecklist.site.latitude} />
                                        <DetailItem label="Longitude" value={outdoorAreaChecklist.site.longitude} />
                                        <DetailItem label="Potential Obstacle" value={outdoorAreaChecklist.site.potential_obstacle} />
                                    </div>

                                    {outdoorAreaChecklist.site.quality_parameter && renderQualityParameterTable(outdoorAreaChecklist.site.quality_parameter)}
                                </CardContent>
                            </Card>
                        )}

                        {/* Card Sarana Penunjang Outdoor */}
                        {outdoorAreaChecklist.sarana_penunjang && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Package className="h-5 w-5 text-green-500" />
                                        Sarana Penunjang
                                    </CardTitle>
                                    <CardDescription>Mounting dan penangkal petir</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-3">
                                        <DetailItem label="Type Mounting" value={outdoorAreaChecklist.sarana_penunjang.type_mounting} />
                                        <DetailItem label="Tinggi Mounting" value={outdoorAreaChecklist.sarana_penunjang.tinggi_mounting} />
                                        <DetailItem label="Type Penangkal Petir" value={outdoorAreaChecklist.sarana_penunjang.type_penangkal_petir} />
                                    </div>

                                    {outdoorAreaChecklist.sarana_penunjang.quality_parameter &&
                                        renderQualityParameterTable(outdoorAreaChecklist.sarana_penunjang.quality_parameter)}
                                </CardContent>
                            </Card>
                        )}

                        {/* Card Perangkat Antenna */}
                        {outdoorAreaChecklist.perangkat_antenna && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Radio className="h-5 w-5 text-indigo-500" />
                                        Perangkat Antenna
                                    </CardTitle>
                                    <CardDescription>Spesifikasi dan kondisi antenna</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-3">
                                        <DetailItem label="Polarisasi" value={outdoorAreaChecklist.perangkat_antenna.polarisasi} />
                                        <DetailItem label="Altitude" value={outdoorAreaChecklist.perangkat_antenna.altitude} />
                                        <DetailItem label="Lokasi" value={outdoorAreaChecklist.perangkat_antenna.lokasi} />
                                    </div>

                                    {outdoorAreaChecklist.perangkat_antenna.quality_parameter &&
                                        renderQualityParameterTable(outdoorAreaChecklist.perangkat_antenna.quality_parameter)}
                                </CardContent>
                            </Card>
                        )}

                        {/* Card Cabling Installation */}
                        {outdoorAreaChecklist.cabling_installation && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Network className="h-5 w-5 text-cyan-500" />
                                        Cabling Installation
                                    </CardTitle>
                                    <CardDescription>Instalasi kabel IFL</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-3">
                                        <DetailItem label="Type Kabel IFL" value={outdoorAreaChecklist.cabling_installation.type_kabel_ifl} />
                                        <DetailItem label="Panjang Kabel IFL" value={outdoorAreaChecklist.cabling_installation.panjang_kabel_ifl} />
                                        <DetailItem
                                            label="Tahanan Short Kabel IFL"
                                            value={outdoorAreaChecklist.cabling_installation.tahanan_short_kabel_ifl}
                                        />
                                    </div>

                                    {outdoorAreaChecklist.cabling_installation.quality_parameter &&
                                        renderQualityParameterTable(outdoorAreaChecklist.cabling_installation.quality_parameter)}
                                </CardContent>
                            </Card>
                        )}
                    </div>
                </>
            )}
        </div>
    );
}
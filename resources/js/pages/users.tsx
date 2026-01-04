import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { Head, router, useForm } from '@inertiajs/react';
import { Calendar, CheckCircle2, Mail, MoreHorizontal, Pencil, Shield, Trash2, UserPlus, XCircle } from 'lucide-react';
import { useState } from 'react';
import { toast } from 'sonner';

// ✅ Update interface User
interface User {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'engineer' | 'nms'; // ✅ Update roles
    avatar?: string;
    email_verified_at: string | null;
    is_verified_by_admin: boolean; // ✅ Tambah
    verified_by: number | null; // ✅ Tambah
    verified_at: string | null; // ✅ Tambah
    created_at: string;
    updated_at: string;
}

// ✅ Update interface Statistics
interface Statistics {
    total: number;
    verified: number;
    pending: number; // ✅ Tambah
    admins: number;
    engineers: number; // ✅ Tambah
    nms: number; // ✅ Tambah
}

// ✅ Props dari backend
interface Props {
    users: User[];
    statistics: Statistics;
}

export default function Users({ users: initialUsers, statistics }: Props) {
    const [openDropdown, setOpenDropdown] = useState<number | null>(null);
    
    // ✅ Gabungkan dialog state menjadi satu
    const [dialogState, setDialogState] = useState<{
        isOpen: boolean;
        mode: 'add' | 'edit';
        editingUser: User | null;
    }>({
        isOpen: false,
        mode: 'add',
        editingUser: null,
    });

    // ✅ Gabungkan confirmation dialog state menjadi satu
    const [confirmDialog, setConfirmDialog] = useState<{
        isOpen: boolean;
        type: 'delete' | 'verify' | null;
        user: User | null;
    }>({
        isOpen: false,
        type: null,
        user: null,
    });


    // ✅ Gunakan Inertia useForm
    const {
        data,
        setData,
        post,
        put,
        delete: destroy,
        processing,
        errors,
        reset,
    } = useForm({
        name: '',
        email: '',
        role: 'engineer' as 'admin' | 'engineer' | 'nms',
        password: '',
        is_verified_by_admin: false,
    });

    const getInitials = (name: string) => {
        return name
            .split(' ')
            .map((n) => n[0])
            .join('')
            .toUpperCase()
            .slice(0, 2);
    };

    // ✅ Update badge variant untuk role baru
    const getRoleBadgeVariant = (role: string) => {
        switch (role) {
            case 'admin':
                return 'destructive';
            case 'engineer':
                return 'default';
            case 'nms':
                return 'secondary';
            default:
                return 'outline';
        }
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    };

    const openAddDialog = () => {
        reset();
        setDialogState({
            isOpen: true,
            mode: 'add',
            editingUser: null,
        });
    };

    const openEditDialog = (user: User) => {
        setData({
            name: user.name,
            email: user.email,
            role: user.role,
            password: '',
            is_verified_by_admin: user.is_verified_by_admin,
        });
        setDialogState({
            isOpen: true,
            mode: 'edit',
            editingUser: user,
        });
    };

    const closeDialog = () => {
        setDialogState({
            isOpen: false,
            mode: 'add',
            editingUser: null,
        });
        reset();
    };

    // ✅ Handle submit dengan Inertia
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (dialogState.mode === 'add') {
            post(route('users.store'), {
                preserveScroll: true,
                onSuccess: () => {
                    closeDialog();
                    toast.success('User added successfully!', {
                        description: `${data.name} has been added to the system.`,
                    });
                },
                onError: (errors) => {
                    const firstError = Object.values(errors)[0];
                    toast.error('Validation Error', {
                        description: typeof firstError === 'string' ? firstError : 'Please check your input.',
                    });
                },
            });
        } else if (dialogState.editingUser) {
            put(route('users.update', dialogState.editingUser.id), {
                preserveScroll: true,
                onSuccess: () => {
                    closeDialog();
                    toast.success('User updated successfully!', {
                        description: `${data.name} has been updated.`,
                    });
                },
                onError: (errors) => {
                    const firstError = Object.values(errors)[0];
                    toast.error('Validation Error', {
                        description: typeof firstError === 'string' ? firstError : 'Please check your input.',
                    });
                },
            });
        }
    };

    // ✅ Toggle admin verification
    const toggleVerification = (userId: number) => {
        router.patch(
            route('users.toggleAdminVerification', userId),
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success('Verification status updated');
                    setConfirmDialog({ isOpen: false, type: null, user: null });
                },
                onError: () => {
                    toast.error('Failed to update verification status');
                    setConfirmDialog({ isOpen: false, type: null, user: null });
                },
            },
        );
    };

    const handleVerifyConfirm = (user: User) => {
        setConfirmDialog({
            isOpen: true,
            type: 'verify',
            user: user,
        });
    };

    const confirmVerification = () => {
        if (confirmDialog.user) {
            toggleVerification(confirmDialog.user.id);
        }
    };

    // ✅ Handle delete dengan Inertia
    const handleDelete = (userId: number, userName: string) => {
        destroy(route('users.destroy', userId), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('User deleted successfully!', {
                    description: `${userName} has been removed from the system.`,
                });
                setConfirmDialog({ isOpen: false, type: null, user: null });
            },
            onError: () => {
                toast.error('Failed to delete user. Please try again.');
                setConfirmDialog({ isOpen: false, type: null, user: null });
            },
        });
    };

    const handleDeleteConfirm = (user: User) => {
        setConfirmDialog({
            isOpen: true,
            type: 'delete',
            user: user,
        });
    };

    const confirmDelete = () => {
        if (confirmDialog.user) {
            handleDelete(confirmDialog.user.id, confirmDialog.user.name);
        }
    };

    // ✅ Handler untuk menutup dialog
    const closeConfirmDialog = () => {
        setConfirmDialog({ isOpen: false, type: null, user: null });
    };

    return (
        <AppLayout>
            <Head title="Users Management" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-4">
                        <div>
                            <CardTitle className="text-2xl font-bold">Users Management</CardTitle>
                            <CardDescription>Manage system users and their roles</CardDescription>
                        </div>
                        <Button className="cursor-pointer gap-2" onClick={openAddDialog} disabled={processing}>
                            <UserPlus className="h-4 w-4" />
                            Add User
                        </Button>
                    </CardHeader>
                    <CardContent>
                        {/* ✅ Statistics dengan data dari backend */}
                        <div className="mt-6 grid gap-4 md:grid-cols-4">
                            <Card>
                                <CardHeader className="pb-3">
                                    <CardDescription>Total Users</CardDescription>
                                    <CardTitle className="text-3xl">{statistics.total}</CardTitle>
                                </CardHeader>
                            </Card>
                            <Card>
                                <CardHeader className="pb-3">
                                    <CardDescription>Verified</CardDescription>
                                    <CardTitle className="text-3xl text-green-600">{statistics.verified}</CardTitle>
                                </CardHeader>
                            </Card>
                            <Card>
                                <CardHeader className="pb-3">
                                    <CardDescription>Pending</CardDescription>
                                    <CardTitle className="text-3xl text-yellow-600">{statistics.pending}</CardTitle>
                                </CardHeader>
                            </Card>
                            <Card>
                                <CardHeader className="pb-3">
                                    <CardDescription>Admins</CardDescription>
                                    <CardTitle className="text-3xl">{statistics.admins}</CardTitle>
                                </CardHeader>
                            </Card>
                        </div>

                        <div className="mt-8 rounded-lg border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead className="w-[80px]">ID</TableHead>
                                        <TableHead>User</TableHead>
                                        <TableHead>Email</TableHead>
                                        <TableHead>Role</TableHead>
                                        <TableHead>Verification Status</TableHead>
                                        <TableHead>Joined</TableHead>
                                        <TableHead className="text-right">Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {initialUsers.map((user) => (
                                        <TableRow key={user.id}>
                                            <TableCell className="font-medium">{user.id}</TableCell>
                                            <TableCell>
                                                <div className="flex items-center gap-3">
                                                    <Avatar className="h-10 w-10">
                                                        <AvatarImage src={user.avatar} alt={user.name} />
                                                        <AvatarFallback className="bg-primary/10 text-primary">
                                                            {getInitials(user.name)}
                                                        </AvatarFallback>
                                                    </Avatar>
                                                    <div>
                                                        <div className="font-medium">{user.name}</div>
                                                        <div className="text-xs text-muted-foreground">ID: {user.id}</div>
                                                    </div>
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex items-center gap-2">
                                                    <Mail className="h-4 w-4 text-muted-foreground" />
                                                    <span className="text-sm">{user.email}</span>
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <Badge variant={getRoleBadgeVariant(user.role)} className="capitalize">
                                                    <Shield className="mr-1 h-3 w-3" />
                                                    {user.role}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>
                                                {/* ✅ Update: Tampilkan admin verification status */}
                                                {user.is_verified_by_admin ? (
                                                    <div className="flex items-center gap-2">
                                                        <CheckCircle2 className="h-4 w-4 text-green-600" />
                                                        <Badge variant="outline" className="border-green-500 text-green-600">
                                                            Verified by Admin
                                                        </Badge>
                                                    </div>
                                                ) : (
                                                    <div className="flex items-center gap-2">
                                                        <XCircle className="h-4 w-4 text-yellow-600" />
                                                        <Badge variant="outline" className="border-yellow-500 text-yellow-600">
                                                            Pending Approval
                                                        </Badge>
                                                    </div>
                                                )}
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex items-center gap-2 text-sm text-muted-foreground">
                                                    <Calendar className="h-4 w-4" />
                                                    {formatDate(user.created_at)}
                                                </div>
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <div className="relative">
                                                    <Button
                                                        variant="ghost"
                                                        className="h-8 w-8 cursor-pointer p-0"
                                                        onClick={() => setOpenDropdown(openDropdown === user.id ? null : user.id)}
                                                    >
                                                        <span className="sr-only">Open menu</span>
                                                        <MoreHorizontal className="h-4 w-4" />
                                                    </Button>

                                                    {openDropdown === user.id && (
                                                        <>
                                                            <div className="fixed inset-0 z-40" onClick={() => setOpenDropdown(null)} />
                                                            <div className="absolute top-full right-0 z-50 mt-1 min-w-[200px] rounded-md border bg-popover p-1 text-popover-foreground shadow-lg">
                                                                <div className="border-b border-border py-2 text-center text-sm font-semibold">
                                                                    Actions
                                                                </div>

                                                                {/* ✅ Toggle Verification Button */}
                                                                <button
                                                                    className="mt-1 flex w-full cursor-pointer items-center rounded-sm px-3 py-2 text-sm transition-colors hover:bg-accent hover:text-accent-foreground"
                                                                    onClick={() => {
                                                                        setOpenDropdown(null);
                                                                        handleVerifyConfirm(user); // ✅ PERBAIKI: Panggil handleVerifyConfirm bukan langsung toggleVerification
                                                                    }}
                                                                >
                                                                    {user.is_verified_by_admin ? (
                                                                        <>
                                                                            <XCircle className="mr-2 h-4 w-4 text-yellow-600" />
                                                                            Revoke Verification
                                                                        </>
                                                                    ) : (
                                                                        <>
                                                                            <CheckCircle2 className="mr-2 h-4 w-4 text-green-600" />
                                                                            Verify User
                                                                        </>
                                                                    )}
                                                                </button>

                                                                <button
                                                                    className="flex w-full cursor-pointer items-center rounded-sm px-3 py-2 text-sm transition-colors hover:bg-accent hover:text-accent-foreground"
                                                                    onClick={() => {
                                                                        setOpenDropdown(null);
                                                                        openEditDialog(user);
                                                                    }}
                                                                >
                                                                    <Pencil className="mr-2 h-4 w-4" />
                                                                    Edit User
                                                                </button>

                                                                <button
                                                                    className="flex w-full cursor-pointer items-center rounded-sm px-3 py-2 text-sm text-red-600 transition-colors hover:bg-destructive/10"
                                                                    onClick={() => {
                                                                        setOpenDropdown(null);
                                                                        handleDeleteConfirm(user);
                                                                    }}
                                                                >
                                                                    <Trash2 className="mr-2 h-4 w-4" />
                                                                    Delete User
                                                                </button>
                                                            </div>
                                                        </>
                                                    )}
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                </Card>
            </div>
            {/* Dialog Add/Edit */}
            <Dialog open={dialogState.isOpen} onOpenChange={closeDialog}>
                <DialogContent className="sm:max-w-[500px]">
                    <form onSubmit={handleSubmit}>
                        <DialogHeader>
                            <DialogTitle>{dialogState.mode === 'add' ? 'Add New User' : 'Edit User'}</DialogTitle>
                            <DialogDescription>
                                {dialogState.mode === 'add'
                                    ? 'Create a new user account. User needs admin verification to login.'
                                    : 'Update user account information. Password is optional.'}
                            </DialogDescription>
                        </DialogHeader>
                        <div className="grid gap-4 py-4">
                            <div className="grid gap-2">
                                <Label htmlFor="name">
                                    Name <span className="text-red-500">*</span>
                                </Label>
                                <Input
                                    id="name"
                                    placeholder="John Doe"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    className={errors.name ? 'border-red-500' : ''}
                                />
                                {errors.name && <p className="text-sm text-red-500">{errors.name}</p>}
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="email">
                                    Email <span className="text-red-500">*</span>
                                </Label>
                                <Input
                                    id="email"
                                    type="email"
                                    placeholder="john@company.com"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    className={errors.email ? 'border-red-500' : ''}
                                />
                                {errors.email && <p className="text-sm text-red-500">{errors.email}</p>}
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="password">
                                    Password {dialogState.mode === 'add' && <span className="text-red-500">*</span>}
                                    {dialogState.mode === 'edit' && (
                                        <span className="ml-1 text-xs text-muted-foreground">(Leave empty to keep current)</span>
                                    )}
                                </Label>
                                <Input
                                    id="password"
                                    type="password"
                                    placeholder="••••••••"
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                    className={errors.password ? 'border-red-500' : ''}
                                />
                                {errors.password && <p className="text-sm text-red-500">{errors.password}</p>}
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="role">
                                    Role <span className="text-red-500">*</span>
                                </Label>
                                <Select value={data.role} onValueChange={(value: any) => setData('role', value)}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select a role" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="engineer">Engineer</SelectItem>
                                        <SelectItem value="nms">NMS</SelectItem>
                                        <SelectItem value="admin">Admin</SelectItem>
                                    </SelectContent>
                                </Select>
                                {errors.role && <p className="text-sm text-red-500">{errors.role}</p>}
                            </div>
                            {/* ✅ Admin Verification Switch (hanya untuk Add) */}
                            {dialogState.mode === 'add' && (
                                <div className="flex items-center justify-between rounded-lg border p-4">
                                    <div className="space-y-0.5">
                                        <Label htmlFor="isVerifiedByAdmin">Admin Verification</Label>
                                        <p className="text-sm text-muted-foreground">Verify this user immediately (skip pending approval)</p>
                                    </div>
                                    <Switch
                                        id="isVerifiedByAdmin"
                                        checked={data.is_verified_by_admin}
                                        onCheckedChange={(checked) => setData('is_verified_by_admin', checked)}
                                    />
                                </div>
                            )}
                        </div>
                        <DialogFooter>
                            <Button type="button" variant="outline" onClick={closeDialog} disabled={processing}>
                                Cancel
                            </Button>
                            <Button type="submit" disabled={processing}>
                                {processing ? 'Processing...' : dialogState.mode === 'add' ? 'Add User' : 'Update User'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
            {/* Alert Dialog Gabungan untuk Toggle Verification & Delete User */}
            <AlertDialog open={confirmDialog.isOpen} onOpenChange={closeConfirmDialog}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>
                            {confirmDialog.type === 'delete' && `Delete ${confirmDialog.user?.name}?`}
                            {confirmDialog.type === 'verify' && (confirmDialog.user?.is_verified_by_admin ? 'Revoke Verification?' : 'Verify User?')}
                        </AlertDialogTitle>
                        <AlertDialogDescription>
                            {confirmDialog.type === 'delete' && (
                                <>
                                    Are you sure you want to delete "{confirmDialog.user?.name}"? This action cannot be undone and will permanently remove the user from the system.
                                </>
                            )}
                            {confirmDialog.type === 'verify' && confirmDialog.user?.is_verified_by_admin && (
                                <>
                                    Are you sure you want to revoke admin verification for "{confirmDialog.user?.name}"? This user will no longer be able to access the system until verified again.
                                </>
                            )}
                            {confirmDialog.type === 'verify' && !confirmDialog.user?.is_verified_by_admin && (
                                <>
                                    Are you sure you want to verify "{confirmDialog.user?.name}"? This will grant them access to the system.
                                </>
                            )}
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel onClick={closeConfirmDialog}>Cancel</AlertDialogCancel>
                        <AlertDialogAction
                            onClick={confirmDialog.type === 'delete' ? confirmDelete : confirmVerification}
                            className={`cursor-pointer ${
                                confirmDialog.type === 'delete'
                                    ? 'bg-red-600 text-white hover:bg-red-700'
                                    : confirmDialog.user?.is_verified_by_admin
                                    ? 'bg-yellow-600 text-white hover:bg-yellow-700'
                                    : 'bg-green-600 text-white hover:bg-green-700'
                            }`}
                        >
                            {confirmDialog.type === 'delete' && 'Delete User'}
                            {confirmDialog.type === 'verify' && (confirmDialog.user?.is_verified_by_admin ? 'Revoke Verification' : 'Verify User')}
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </AppLayout>
    );
}

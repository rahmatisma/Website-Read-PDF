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
import { Head } from '@inertiajs/react';
import { Calendar, CheckCircle2, Mail, MoreHorizontal, Pencil, Shield, Trash2, UserPlus, XCircle } from 'lucide-react';
import { useState } from 'react';
import { toast } from 'sonner';

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

interface UserForm {
    name: string;
    email: string;
    role: string;
    password: string;
    emailVerified: boolean;
}

// Data Dummy Users
const dummyUsers: User[] = [
    {
        id: 1,
        name: 'Administrator',
        email: 'admin@email.com',
        role: 'admin',
        avatar: undefined,
        email_verified_at: '2024-01-15T10:30:00.000000Z',
        created_at: '2024-01-15T10:30:00.000000Z',
        updated_at: '2024-12-22T08:15:00.000000Z',
    },
    {
        id: 2,
        name: 'John Doe',
        email: 'john@email.com',
        role: 'user',
        avatar: undefined,
        email_verified_at: '2024-03-20T14:20:00.000000Z',
        created_at: '2024-03-20T14:20:00.000000Z',
        updated_at: '2024-12-20T09:30:00.000000Z',
    },
    {
        id: 3,
        name: 'Jane Smith',
        email: 'jane@email.com',
        role: 'user',
        avatar: undefined,
        email_verified_at: '2024-05-10T09:15:00.000000Z',
        created_at: '2024-05-10T09:15:00.000000Z',
        updated_at: '2024-12-21T11:45:00.000000Z',
    },
    {
        id: 4,
        name: 'Bob Manager',
        email: 'bob@email.com',
        role: 'manager',
        avatar: undefined,
        email_verified_at: '2024-02-28T16:45:00.000000Z',
        created_at: '2024-02-28T16:45:00.000000Z',
        updated_at: '2024-12-19T13:20:00.000000Z',
    },
    {
        id: 5,
        name: 'Alice Cooper',
        email: 'alice@email.com',
        role: 'user',
        avatar: undefined,
        email_verified_at: null,
        created_at: '2024-12-01T08:00:00.000000Z',
        updated_at: '2024-12-01T08:00:00.000000Z',
    },
];

export default function Users() {
    const [users, setUsers] = useState<User[]>(dummyUsers);
    const [openDropdown, setOpenDropdown] = useState<number | null>(null);

    const [dialogState, setDialogState] = useState<{
        isOpen: boolean;
        mode: 'add' | 'edit';
        editingUser: User | null;
    }>({
        isOpen: false,
        mode: 'add',
        editingUser: null,
    });

    const [formData, setFormData] = useState<UserForm>({
        name: '',
        email: '',
        role: 'user',
        password: '',
        emailVerified: false,
    });

    const getInitials = (name: string) => {
        return name
            .split(' ')
            .map((n) => n[0])
            .join('')
            .toUpperCase()
            .slice(0, 2);
    };

    const getRoleBadgeVariant = (role: string) => {
        switch (role) {
            case 'admin':
                return 'destructive';
            case 'manager':
                return 'default';
            default:
                return 'secondary';
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
        setFormData({
            name: '',
            email: '',
            role: 'user',
            password: '',
            emailVerified: false,
        });
        setDialogState({
            isOpen: true,
            mode: 'add',
            editingUser: null,
        });
    };

    const openEditDialog = (user: User) => {
        setFormData({
            name: user.name,
            email: user.email,
            role: user.role,
            password: '',
            emailVerified: !!user.email_verified_at,
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
        setFormData({
            name: '',
            email: '',
            role: 'user',
            password: '',
            emailVerified: false,
        });
    };

    const handleSubmit = () => {
        if (!formData.name || !formData.email) {
            toast.error('Validation Error', {
                description: 'Please fill in all required fields.',
            });
            return;
        }

        if (dialogState.mode === 'add' && !formData.password) {
            toast.error('Validation Error', {
                description: 'Password is required.',
            });
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(formData.email)) {
            toast.error('Invalid Email', {
                description: 'Please enter a valid email address.',
            });
            return;
        }

        if (dialogState.mode === 'add') {
            const newUser: User = {
                id: Math.max(...users.map((u) => u.id), 0) + 1,
                name: formData.name,
                email: formData.email,
                role: formData.role,
                avatar: undefined,
                email_verified_at: formData.emailVerified ? new Date().toISOString() : null,
                created_at: new Date().toISOString(),
                updated_at: new Date().toISOString(),
            };

            setUsers([...users, newUser]);
            toast.success('User added successfully!', {
                description: `${newUser.name} has been added to the system.`,
            });
        } else {
            const updatedUsers = users.map((user) =>
                user.id === dialogState.editingUser?.id
                    ? {
                          ...user,
                          name: formData.name,
                          email: formData.email,
                          role: formData.role,
                          email_verified_at: formData.emailVerified ? user.email_verified_at || new Date().toISOString() : null,
                          updated_at: new Date().toISOString(),
                      }
                    : user,
            );

            setUsers(updatedUsers);
            toast.success('User updated successfully!', {
                description: `${formData.name} has been updated.`,
            });
        }

        closeDialog();
    };

    const toggleVerification = (userId: number, currentStatus: string | null) => {
        const user = users.find((u) => u.id === userId);
        if (!user) return;

        const newStatus = currentStatus ? null : new Date().toISOString();
        const updatedUsers = users.map((u) => (u.id === userId ? { ...u, email_verified_at: newStatus, updated_at: new Date().toISOString() } : u));

        setUsers(updatedUsers);
        toast.success(`Email ${newStatus ? 'verified' : 'unverified'}`, {
            description: `${user.name}'s email has been ${newStatus ? 'verified' : 'marked as unverified'}.`,
        });
    };

    const handleDeleteConfirm = (user: User) => {
        toast.warning(`Delete ${user.name}?`, {
            description: `Are you sure you want to delete "${user.name}"? This action cannot be undone.`,
            action: {
                label: 'Delete',
                onClick: () => handleDelete(user.id, user.name),
            },
            cancel: {
                label: 'Cancel',
                onClick: () => toast.info('Delete cancelled'),
            },
            duration: 10000,
        });
    };

    const handleDelete = (id: number, name: string) => {
        setUsers(users.filter((user) => user.id !== id));
        toast.success('User deleted successfully!', {
            description: `${name} has been removed from the system.`,
        });
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
                        <Button className="cursor-pointer gap-2" onClick={openAddDialog}>
                            <UserPlus className="h-4 w-4" />
                            Add User
                        </Button>
                    </CardHeader>
                    <CardContent>
                        {/* Statistics */}
                        <div className="mt-6 grid gap-4 md:grid-cols-3">
                            <Card>
                                <CardHeader className="pb-3">
                                    <CardDescription>Total Users</CardDescription>
                                    <CardTitle className="text-3xl">{users.length}</CardTitle>
                                </CardHeader>
                            </Card>
                            <Card>
                                <CardHeader className="pb-3">
                                    <CardDescription>Verified</CardDescription>
                                    <CardTitle className="text-3xl">{users.filter((u) => u.email_verified_at).length}</CardTitle>
                                </CardHeader>
                            </Card>
                            <Card>
                                <CardHeader className="pb-3">
                                    <CardDescription>Admins</CardDescription>
                                    <CardTitle className="text-3xl">{users.filter((u) => u.role === 'admin').length}</CardTitle>
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
                                        <TableHead>Status</TableHead>
                                        <TableHead>Joined</TableHead>
                                        <TableHead className="text-right">Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {users.map((user) => (
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
                                                {user.email_verified_at ? (
                                                    <div className="flex items-center gap-2">
                                                        <CheckCircle2 className="h-4 w-4 text-green-600" />
                                                        <Badge variant="outline" className="border-green-500 text-green-600">
                                                            Verified
                                                        </Badge>
                                                    </div>
                                                ) : (
                                                    <div className="flex items-center gap-2">
                                                        <XCircle className="h-4 w-4 text-yellow-600" />
                                                        <Badge variant="outline" className="border-yellow-500 text-yellow-600">
                                                            Unverified
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
                                                            <div className="relative top-full right-0 z-50 mt-1 min-w-[160px] rounded-md border bg-popover p-1 text-popover-foreground shadow-lg">
                                                                <div className="border-b border-border py-2 text-center text-sm font-semibold">
                                                                    Actions
                                                                </div>

                                                                <button
                                                                    className="mt-1 flex w-full cursor-pointer items-center rounded-sm px-3 py-2 text-sm transition-colors hover:bg-accent hover:text-accent-foreground"
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

            {/* Dialog */}
            <Dialog open={dialogState.isOpen} onOpenChange={closeDialog}>
                <DialogContent className="sm:max-w-[500px]">
                    <DialogHeader>
                        <DialogTitle>{dialogState.mode === 'add' ? 'Add New User' : 'Edit User'}</DialogTitle>
                        <DialogDescription>
                            {dialogState.mode === 'add'
                                ? 'Create a new user account. Fill in all required fields.'
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
                                value={formData.name}
                                onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                            />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="email">
                                Email <span className="text-red-500">*</span>
                            </Label>
                            <Input
                                id="email"
                                type="email"
                                placeholder="john@example.com"
                                value={formData.email}
                                onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                            />
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
                                value={formData.password}
                                onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                            />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="role">Role</Label>
                            <Select value={formData.role} onValueChange={(value) => setFormData({ ...formData, role: value })}>
                                <SelectTrigger>
                                    <SelectValue placeholder="Select a role" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="user">User</SelectItem>
                                    <SelectItem value="manager">Manager</SelectItem>
                                    <SelectItem value="admin">Admin</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div className="flex items-center justify-between rounded-lg border p-4">
                            <div className="space-y-0.5">
                                <Label htmlFor="emailVerified">Email Verification Status</Label>
                                <p className="text-sm text-muted-foreground">Mark this email as verified</p>
                            </div>
                            <Switch
                                id="emailVerified"
                                checked={formData.emailVerified}
                                onCheckedChange={(checked) => setFormData({ ...formData, emailVerified: checked })}
                            />
                        </div>
                    </div>
                    <DialogFooter>
                        <Button variant="outline" onClick={closeDialog}>
                            Cancel
                        </Button>
                        <Button onClick={handleSubmit}>{dialogState.mode === 'add' ? 'Add User' : 'Update User'}</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}

export const ROLES =[
    {id: 1,name: "Admin"},
    {id: 2, name: "Bác sĩ"},
    {id: 3, name: "Nhân viên Y tế"}
]
export const getRoleName = (id) => {
    const role = ROLES.find(r => r.id === id)
    return role ? role.name: "Không xác định"
}
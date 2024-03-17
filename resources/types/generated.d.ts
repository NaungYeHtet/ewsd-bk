declare namespace App.Data {
export type AcademicData = {
id?: string;
academicYear: string;
startDate: string;
closureDate: string;
finalClosureDate: string;
isActive?: boolean;
};
export type CategoryData = {
name: string;
slug?: string;
};
export type CommentData = {
id?: string;
content: string;
isAnonymous?: boolean;
staff?: App.Data.StaffData;
submittedAt?: string;
};
export type DepartmentData = {
name: string;
slug?: string;
};
export type FileType = {
url: string;
type: string;
};
export type IdeaData = {
slug: string;
title: string;
content: string;
file: App.Data.FileType | null;
staff?: App.Data.StaffData;
reactionsCount: Array<any>;
viewsCount?: number;
commentsCount: number;
currentReaction: any | null;
category?: App.Data.CategoryData;
submittedAt: string;
};
export type RoleData = {
name: string;
};
export type SidebarData = {
title: string;
key: string | null;
icon: string;
url: string;
permissions: Array<any>;
reactionPermissions?: Array<any>;
commentPermissions?: Array<any>;
};
export type StaffData = {
id?: string;
name: string;
email: string;
username?: string;
avatar?: string | null;
disabledAt: string | null;
lastLoggedInAt: string | null;
role?: string;
department?: App.Data.DepartmentData;
};
}

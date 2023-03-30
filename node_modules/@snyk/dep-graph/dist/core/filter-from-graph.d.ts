import { DepGraph, PkgInfo } from './types';
declare type NodeId = string;
declare type PkgName = string;
export declare function filterPackagesFromGraph(originalDepGraph: DepGraph, packagesToFilterOut: (PkgName | PkgInfo)[]): Promise<DepGraph>;
export declare function filterNodesFromGraph(originalDepGraph: DepGraph, nodeIdsToFilterOut: NodeId[]): Promise<DepGraph>;
export {};

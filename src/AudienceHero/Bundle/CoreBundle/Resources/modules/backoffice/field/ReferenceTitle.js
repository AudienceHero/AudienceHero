import React from 'react';

export const ReferenceTitle = ({ record }) => {
    return <span>{record ? `${record.reference}` : ''}</span>;
};

export default ReferenceTitle;

import FileUploadCard from './FileUploadCard';
import { Card, CardTitle, CardText } from 'material-ui/Card';
import {LinearProgress} from 'material-ui/Progress';
import React from 'react';
import { shallow } from 'enzyme';

const fakeFile = new File(['foo'], 'Filename.txt');
test('FileUploadCard renders correctly', () => {
    const wrapper = shallow(<FileUploadCard file={fakeFile} />);
    expect(wrapper.children(CardTitle)).toHaveLength(1);
    expect(wrapper.children(CardTitle).prop('children')).toBe('Filename.txt');
    expect(wrapper.find(LinearProgress).prop('mode')).toBe('determinate');
});

test('FileUploadCard renders progress', () => {
    const wrapper = shallow(<FileUploadCard progress={0.4} file={fakeFile} />);
    expect(wrapper.find(LinearProgress).prop('value')).toBe(40);
});

test('FileUploadCard becomes indeterminate at end of upload', () => {
    const wrapper = shallow(<FileUploadCard progress={1} file={fakeFile} />);
    expect(wrapper.find(LinearProgress).prop('value')).toBe(100);
    expect(wrapper.find(LinearProgress).prop('mode')).toBe('indeterminate');
});

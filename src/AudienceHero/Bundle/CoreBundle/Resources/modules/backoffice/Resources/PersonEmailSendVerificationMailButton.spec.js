import React from 'react';
import { shallow } from 'enzyme';
import { PersonEmailSendVerificationMailButton } from './PersonEmailSendVerificationMailButton';
import Button from "material-ui/Button"
import { stub, spy } from 'sinon';

test('Button does not render is isVerified is true', () => {
    const props = {
        translate: spy(),
        record: { isVerified: true },
        sendPersonEmailVerification: spy(),
    };

    const wrapper = shallow(
        <PersonEmailSendVerificationMailButton {...props} />
    );
    expect(props.translate.called).toBe(false);
    expect(props.sendPersonEmailVerification.called).toBe(false);
    expect(wrapper.html()).toBe(null);
});

test('Button does render when isVerified is false', () => {
    const translate = stub();
    const action = spy();
    const record = { id: 'myid', isVerified: false };
    const props = { translate, sendPersonEmailVerification: action, record };

    translate
        .withArgs('ah.person_email.send_verification_email')
        .returns('mylabel');

    const wrapper = shallow(
        <PersonEmailSendVerificationMailButton {...props} />
    );
    expect(translate.called).toBe(true);
    expect(
        translate.calledWith('ah.person_email.send_verification_email')
    ).toBe(true);

    expect(wrapper.find(Button)).toHaveLength(1);
    const button = wrapper.find(Button).first();
    expect(button.prop('label')).toBe('mylabel');
    expect(action.called).toBe(false);
    button.simulate('touchTap');
    expect(action.called).toBe(true);
    expect(action.args[0][0]).toEqual('myid');
});

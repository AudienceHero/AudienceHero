import React from 'react';
import { mount, render, shallow } from 'enzyme';
import { PersonEmailVerify } from './PersonEmailVerify';
import { spy, stub } from 'sinon';
import { MuiThemeProvider } from 'material-ui';

test('PersonEmailVerify', () => {
    const translate = stub();
    translate.withArgs('ah.person_email.verifying').returns('mytitle');
    translate.withArgs('ah.person_email.please_wait').returns('mysubtitle');

    const verifyPersonEmail = spy();

    const props = {
        match: {
            params: {
                id: 'myid',
                confirmationToken: 'mytoken',
            },
        },
        verifyPersonEmail,
        translate,
    };

    const wrapper = mount(
        <MuiThemeProvider>
            <PersonEmailVerify {...props} />
        </MuiThemeProvider>
    );
    expect(translate.called).toBe(true);
    expect(translate.args[0][0]).toBe('ah.person_email.verifying');
    expect(translate.args[1][0]).toBe('ah.person_email.please_wait');
    expect(verifyPersonEmail.called).toBe(true);
    expect(verifyPersonEmail.args[0][0]).toBe('myid');
    expect(verifyPersonEmail.args[0][1]).toBe('mytoken');
});

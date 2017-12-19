import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-admin';
import { getImporters } from './importer';
import { Row, Col } from 'react-flexbox-grid';

export class ImportList extends React.Component {
    render() {
        const { translate } = this.props;
        const importers = this.props.getImporters();
        return (
            <div>
                <h1>{translate('ah.core.title.import')}</h1>
                {importers.map((item, index) => {
                    return (
                        <Row style={{ marginBottom: '1em' }}>
                            <Col xs={12} sm={12} lg={12} md={12}>
                                {React.createElement(item, { key: index })}
                            </Col>
                        </Row>
                    );
                })}
            </div>
        );
    }
}

ImportList.propTypes = {
    translate: PropTypes.func.isRequired,
    getImporters: PropTypes.func.isRequired,
};

const enhance = translate;

const EnhanceImportList = enhance(ImportList);

EnhanceImportList.defaultProps = {
    getImporters,
};

export default EnhanceImportList;

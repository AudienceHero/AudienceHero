import React from 'react';
import PropTypes from 'prop-types';
import Dialog, { DialogTitle } from 'material-ui/Dialog';
import {
    FacebookIcon,
    TelegramIcon,
    WhatsAppIcon,
    VKIcon,
    MailIcon,
    RedditIcon,
    TwitterIcon,
} from '../icons';
import List, { ListItem, ListItemText } from 'material-ui/List';
import translate from '../i18n/translate';
import { stringify } from 'qs';

class ShareDialog extends React.Component {
    render() {
        const { shareUrl, title } = this.props;

        return (
            <Dialog
                onRequestClose={this.props.onRequestClose}
                open={this.props.open}
            >
                <DialogTitle>
                    {translate('ah.core.dialog.share.title')}
                </DialogTitle>
                <div>
                    <List>
                        <ListItem
                            button
                            component="a"
                            target="_blank"
                            href={
                                'https://facebook.com/sharer/sharer.php?' +
                                stringify({ url: shareUrl })
                            }
                        >
                            <FacebookIcon />
                            <ListItemText primary="Facebook" />
                        </ListItem>
                        <ListItem
                            button
                            component="a"
                            target="_blank"
                            href={
                                'https://twitter.com/share?' +
                                stringify({ url: shareUrl, text: title })
                            }
                        >
                            <TwitterIcon />
                            <ListItemText primary="Twitter" />
                        </ListItem>
                        <ListItem
                            button
                            component="a"
                            target="_blank"
                            href={
                                'https://www.reddit.com/submit?' +
                                stringify({ url: shareUrl, title: title })
                            }
                        >
                            <RedditIcon />
                            <ListItemText primary="Reddit" />
                        </ListItem>
                        <ListItem
                            button
                            component="a"
                            target="_blank"
                            href={
                                'https://telegram.me/share/?' +
                                stringify({ url: shareUrl, text: title })
                            }
                        >
                            <TelegramIcon />
                            <ListItemText primary="Telegram" />
                        </ListItem>
                        <ListItem
                            button
                            component="a"
                            target="_blank"
                            href={
                                'https://api.whatsapp.com/send?' +
                                stringify({ text: `${title} ${shareUrl}` })
                            }
                        >
                            <WhatsAppIcon />
                            <ListItemText primary="WhatsApp" />
                        </ListItem>
                        <ListItem
                            button
                            component="a"
                            target="_blank"
                            href={
                                'mailto:?' +
                                stringify({ subject: title, body: shareUrl })
                            }
                        >
                            <MailIcon />
                            <ListItemText primary="Mail" />
                        </ListItem>
                    </List>
                </div>
            </Dialog>
        );
    }
}

ShareDialog.propTypes = {
    open: PropTypes.bool.isRequired,
    onRequestClose: PropTypes.func.isRequired,
    shareUrl: PropTypes.string.isRequired,
    title: PropTypes.string,
};

export default translate(ShareDialog);
